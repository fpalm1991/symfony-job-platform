<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\ApplicationStatus;
use App\Entity\Job;
use App\Entity\User;
use App\Form\ApplicationResponseType;
use App\Form\ApplicationType;
use App\lib\ApplicationFile;
use App\lib\ApplicationStatusEnum;
use App\lib\FileHelper;
use App\Repository\ApplicationStatusRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ApplicationController extends AbstractController
{

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordGenerator,
    ) {}

    /**
     * @throws RandomException
     * @throws TransportExceptionInterface
     */
    #[Route('/apply/{id<\d+>}', name: 'app_job_application_apply', methods: ['GET', 'POST'])]
    public function applyForJob(
        Job $job,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/var/uploads/applications')] string $applicationFilesDirectory,
        MailerInterface $mailer,
        ApplicationStatusRepository $applicationStatusRepository,
    ): Response
    {
        // Create the application form
        $form = $this->createForm(ApplicationType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // Creating a new Application
            $application = new Application();

            // Retrieve data from the form
            $formData = $form->getData();

            // Handle file uploads
            $cvFile = $form->get('curriculum_vitae')->getData();
            $letter_of_motivation = $form->get('letter_of_motivation')->getData();

            $successfulFileUpload = false;

            if ($cvFile) {
                $successfulFileUpload = FileHelper::saveFileOfApplication(
                    application: $application,
                    applicationFileType: ApplicationFile::CV,
                    fileName: $cvFile,
                    slugger: $slugger,
                    applicationFilesDirectory: $applicationFilesDirectory
                );
            }

            if ( ! $successfulFileUpload ) {
                return $this->redirectToRoute('app_index');
            }

            if ($letter_of_motivation) {
                $successfulFileUpload = FileHelper::saveFileOfApplication(
                    application: $application,
                    applicationFileType: ApplicationFile::Motivation,
                    fileName: $letter_of_motivation,
                    slugger: $slugger,
                    applicationFilesDirectory: $applicationFilesDirectory
                );
            }

            if ( ! $successfulFileUpload ) {
                return $this->redirectToRoute('app_index');
            }

            // Set applicant details from form
            $applicantData = $formData->getApplicant();

            // Try to find user in database
            $user = $userRepository->findByEmail($applicantData->getEmail());

            // If new user, create new user and update user data
            if ( ! $user ) {
                $user = new User();
                $user->setFirstName($applicantData->getFirstName());
                $user->setLastName($applicantData->getLastName());
                $user->setEmail($applicantData->getEmail());

                // Generate a temporary password for the applicant
                $temporaryPassword = bin2hex(random_bytes(20));
                $hashedPassword = $this->passwordGenerator->hashPassword($user, $temporaryPassword);
                $user->setPassword($hashedPassword);

                // Assign ROLE_APPLICANT to the user
                $user->setRoles(['ROLE_APPLICANT']);
            }

            // Set application status to pending (communication with applicant)
            $application->setApplicationStatus($applicationStatusRepository->findStatusById(ApplicationStatusEnum::Pending->value));

            // Set application status to unarchived (to be able to archive applications later)
            $application->setIsArchived(false);

            // Set the Job associated with the Application
            $application->setJob($job);
            $application->setApplicant($user);

            // Persist the user and application
            $entityManager->persist($user);
            $entityManager->persist($application);
            $entityManager->flush();

            // Send confirmation email to applicant
            $email = (new TemplatedEmail())
                ->from(new Address('application@example.com', 'Test Company'))
                ->to($applicantData->getEmail())
                ->subject('Your application has been submitted')
                ->htmlTemplate('emails/application.html.twig')
                ->context(['job' => $job, 'application' => $application]);

            $mailer->send($email);

            // Send update email to admins
            $admins = $userRepository->findAdmins();

            foreach ($admins as $admin) {
                $email = (new TemplatedEmail())
                    ->from(new Address('application@example.com', 'Test Company'))
                    ->to($admin->getEmail())
                    ->subject("New Application for " . $job->getTitle())
                    ->htmlTemplate('emails/application.html.twig')
                    ->context(['job' => $job, 'application' => $application]);

                $mailer->send($email);
            }

            return $this->redirectToRoute('app_job_application_thank_you');
        }

        // Render the application form
        return $this->render('application/apply.html.twig', [
            'form' => $form->createView(),
            'job' => $job,
        ]);
    }

    #[Route('/apply/thank-you', name: 'app_job_application_thank_you', methods: ['GET'])]
    public function thankYou(): Response {
        return $this->render('application/thank_you.html.twig');
    }

    #[Route('/show-application-document/{fileName}}', name: 'show_application_document', methods: ['GET'])]
    public function showApplicationDocument(string $fileName): Response
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/var/uploads/applications/' . $fileName;

        if ( ! file_exists($filePath) ) {
            throw new FileNotFoundException('File not found.');
        }

        return new Response(
            file_get_contents($filePath),
            200,
            [
                'Content-Type' => mime_content_type($filePath),
                'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
            ]
        );
    }

    #[Route('/applications', name: 'show_applications', methods: ['GET'])]
    public function showApplications(EntityManagerInterface $entityManager): Response {

        $applications = $entityManager->getRepository(Application::class)->getActiveApplications();

        return $this->render('application/index.html.twig', [
            'applications' => $applications,
        ]);
    }

    #[Route('/applications/{id}', name: 'show_single_application', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showApplication(EntityManagerInterface $entityManager, int $id): Response {

        $application = $entityManager->getRepository(Application::class)->find($id);

        return $this->render('application/show.html.twig', [
            'application' => $application,
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/applications/{id}/response', name: 'response_to_application', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function responseToApplication(
        EntityManagerInterface $entityManager,
        int $id,
        Request $request,
        MailerInterface $mailer,
    ): Response {

        $application = $entityManager->getRepository(Application::class)->find($id);

        $form = $this->createForm(ApplicationResponseType::class, null, [
            'applicant_name' => $application->getApplicant()->getFullName(),
            'application_status' => $application->getApplicationStatus(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $formData = $form->getData();
            $applicationStatus = $formData['application_status'];
            $responseMessage = $formData['response'];

            $email = (new TemplatedEmail())
                ->from(new Address('application@example.com', 'Test Company'))
                ->to($application->getApplicant()->getEmail())
                ->subject('Your application has been submitted')
                ->htmlTemplate('emails/answer.html.twig')
                ->context(['responseMessage' => $responseMessage, 'application' => $application]);

            $mailer->send($email);

            $application->setApplicationStatus($applicationStatus);
            $entityManager->persist($application);
            $entityManager->flush();

            return $this->redirectToRoute('show_applications');
        }

        return $this->render('application/response.html.twig', [
            'application' => $application,
            'form' => $form->createView(),
        ]);
    }
}

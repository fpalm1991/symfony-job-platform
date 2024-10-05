<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Job;
use App\Entity\User;
use App\Form\ApplicationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ApplicationController extends AbstractController
{

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordGenerator,
    )
    {}

    /**
     * @throws RandomException
     */
    #[Route('/apply/{id<\d+>}', name: 'app_job_application_apply', methods: ['GET', 'POST'])]
    public function applyForJob(
        Job $job, Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/public/uploads/applications')] string $applicationFilesDirectory,
    ): Response
    {
        // Create the application form
        $form = $this->createForm(ApplicationType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // Creating a new Application and User entity
            $application = new Application();
            $user = new User();

            // Retrieve data from the form
            $formData = $form->getData();

            // Handle file uploads
            $cvFile = $form->get('curriculum_vitae')->getData();

            if ($cvFile) {
                $originalFilename = pathinfo($cvFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$cvFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $cvFile->move($applicationFilesDirectory, $newFilename);
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $application->setCurriculumVitae($newFilename);
            }

            // Set applicant details from form
            $applicantData = $formData->getApplicant();
            $user->setFirstName($applicantData->getFirstName());
            $user->setLastName($applicantData->getLastName());
            $user->setEmail($applicantData->getEmail());

            // Generate a temporary password for the applicant
            $temporaryPassword = bin2hex(random_bytes(20));
            $hashedPassword = $this->passwordGenerator->hashPassword($user, $temporaryPassword);
            $user->setPassword($hashedPassword);

            // Assign ROLE_APPLICANT to the user
            $user->setRoles(['ROLE_APPLICANT']);

            // Set the Job associated with the Application
            $application->setJob($job);
            $application->setApplicant($user);

            // Persist the user and application
            $entityManager->persist($user); // Change to persist $user instead of $applicant
            $entityManager->persist($application);
            $entityManager->flush();

            // TODO: Send the temporary password to the user's email
            // $this->sendEmail($user->getEmail(), $temporaryPassword);

            return $this->redirectToRoute('app_job_application_thank_you');
        }

        // Render the application form
        return $this->render('application/index.html.twig', [
            'form' => $form->createView(),
            'job' => $job,
        ]);
    }

    #[Route('/apply/thank-you', name: 'app_job_application_thank_you', methods: ['GET'])]
    public function thankYou(): Response {
        return $this->render('application/thank_you.html.twig');
    }
}

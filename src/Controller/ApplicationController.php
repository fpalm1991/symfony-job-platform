<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Job;
use App\Entity\User;
use App\Form\ApplicationType;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ApplicationController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @throws RandomException
     */
    #[Route('/apply/{id}', name: 'app_job_apply', methods: ['GET', 'POST'])]
    public function index(Job $job, Request $request, EntityManagerInterface $entityManager): Response
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

            // Set the application data
            $application->setCurriculumVitae($formData->getCurriculumVitae());

            // Set applicant details from form
            $applicantData = $formData->getApplicant();
            $user->setFirstName($applicantData->getFirstName());
            $user->setLastName($applicantData->getLastName());
            $user->setEmail($applicantData->getEmail());

            // Generate a temporary password for the applicant
            $temporaryPassword = bin2hex(random_bytes(20));
            $hashedPassword = $this->passwordHasher->hashPassword($user, $temporaryPassword);
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

            return $this->redirectToRoute('app_index');
        }

        // Render the application form
        return $this->render('application/index.html.twig', [
            'form' => $form->createView(),
            'job' => $job,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApplicationController extends AbstractController
{
    #[Route('/apply/{id}', name: 'app_job_apply', methods: ['GET', 'POST'])]
    public function index(Job $job): Response
    {
        $form = $this->createForm(ApplicationType::class);
        $user = new User();

        return $this->render('application/index.html.twig', [
            'form' => $form,
            'job' => $job,
            'user' => $user,
        ]);

    }
}

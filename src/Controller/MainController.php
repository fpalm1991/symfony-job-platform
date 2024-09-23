<?php

namespace App\Controller;

use App\Entity\Job;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {

        $jobs = $entityManager->getRepository(Job::class)->findAll();

        return $this->render('main/index.html.twig', [
            'jobs' => $jobs,
        ]);
    }

}

<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Job;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {

        $jobs = $entityManager->getRepository(Job::class)->findAllActiveJobsOrderedByDate();

        // Pagination
        $pagination = $paginator->paginate(
            $jobs,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('main/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }


    #[Route('/company-info', name: 'app_company_info', methods: ['GET'])]
    public function companyInfo(EntityManagerInterface $entityManager): Response {
        $companyInformation = $entityManager->getRepository(Company::class)->findAll();

        return $this->json($companyInformation);
    }

}

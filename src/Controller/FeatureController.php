<?php

namespace App\Controller;

use App\Entity\Feature;
use App\Form\FeatureType;
use App\Repository\FeatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/feature')]
class FeatureController extends AbstractController
{
    #[Route('/', name: 'app_feature_index', methods: ['GET'])]
    public function index(FeatureRepository $featureRepository): Response {

        return $this->render('feature/index.html.twig', [
            'features' => $featureRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_feature_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $feature = new Feature();
        $form = $this->createForm(FeatureType::class, $feature);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($feature);
            $entityManager->flush();

            return $this->redirectToRoute('app_feature_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('feature/new.html.twig', [
            'feature' => $feature,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_feature_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Feature $feature, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FeatureType::class, $feature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_feature_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('feature/edit.html.twig', [
            'feature' => $feature,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_feature_delete', methods: ['POST'])]
    public function delete(Request $request, Feature $feature, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$feature->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($feature);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_feature_index', [], Response::HTTP_SEE_OTHER);
    }
}

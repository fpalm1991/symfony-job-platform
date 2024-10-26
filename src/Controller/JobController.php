<?php

namespace App\Controller;

use App\Entity\Job;
use Mpdf\MpdfException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/job')]
final class JobController extends AbstractController
{

    #[Route('/{id}', name: 'app_job_show', methods: ['GET'])]
    public function show(Job $job): Response
    {
        return $this->render('job/show.html.twig', [
            'job' => $job,
        ]);
    }

    /**
     * @throws MpdfException
     */
    #[Route('/pdf/{id}', name: 'app_job_pdf', methods: ['POST'])]
    public function pdf(Job $job): Response {

        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/../../var/tmp']);
        $mpdf->WriteHTML($this->renderView('job/pdf.html.twig', [
            'job' => $job,
        ]));

        // Force a file download with the name given by $filename
        $mpdf->Output("{$job->getTitle()}.pdf", \Mpdf\Output\Destination::DOWNLOAD);

        return $this->redirectToRoute('app_index', [], Response::HTTP_SEE_OTHER);
    }

}

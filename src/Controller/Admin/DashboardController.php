<?php

namespace App\Controller\Admin;

use App\Controller\JobController;
use App\Entity\Application;
use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\Job;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    #[Route('/admin', name: 'app_job_admin')]
    public function index(): Response
    {
        // return parent::index();
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(JobCrudController::class)->generateUrl());


        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Symfony Job Platform');
            // ->setFaviconPath('favicon.svg')
    }

    public function configureMenuItems(): iterable
    {

        yield MenuItem::linkToUrl('Website', 'fas fa-home', $this->generateUrl('app_index'));

        // yield MenuItem::linkToDashboard('Job Dashboard', 'fa fa-home');

        yield MenuItem::section('Job');
        yield MenuItem::linkToCrud('Job', 'fa fa-tags', Job::class);
        yield MenuItem::linkToCrud('Feature', 'fa fa-paperclip', Feature::class);

        yield MenuItem::section('Applications');
        yield MenuItem::linkToCrud('Applications', 'fa fa-file', Application::class);
        yield MenuItem::linkToCrud('Applicants', 'fa fa-users', User::class)
            ->setController(ApplicantCrudController::class);

        yield MenuItem::section('User');
        yield MenuItem::linkToCrud('User', 'fa fa-user', User::class)
            ->setController(UserCrudController::class);

        yield MenuItem::section('Company');
        yield MenuItem::linkToCrud('Company', 'fa fa-building', Company::class);
    }
}

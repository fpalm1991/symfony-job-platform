<?php

namespace App\Controller\Admin;

use App\Entity\ApplicationStatus;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ApplicationStatusCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ApplicationStatus::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::EDIT, Action::DETAIL, Action::DELETE);
    }
}

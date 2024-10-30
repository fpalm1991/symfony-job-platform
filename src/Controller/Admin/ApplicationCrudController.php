<?php

namespace App\Controller\Admin;

use App\Entity\Application;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ApplicationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Application::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [];

        $applicantField = AssociationField::new('applicant')
            ->setCrudController(ApplicantCrudController::class)
            ->setLabel('Applicant');

        $applicationStatusTextField = TextField::new('applicationStatus', 'Application Status')
            ->setLabel('Application Status');

        $applicationStatusAssociationField = AssociationField::new('applicationStatus', 'ApplicationStatus')
            ->setCrudController(ApplicationStatusCrudController::class)
            ->setLabel('Application Status');

        $isArchivedField = BooleanField::new('isArchived', 'Is archived');

        $cvField = TextField::new('curriculum_vitae', 'Curriculum Vitae')
            ->formatValue(fn($value) => sprintf(
                '<a href="%s" target="_blank">%s</a>',
                $this->generateUrl('show_application_document', ['fileName' => $value]),
                'Curriculum Vitae'
            ));

        $motivationField = TextField::new('letter_of_motivation', 'Letter of Motivation')
            ->formatValue(fn($value) => sprintf(
                '<a href="%s" target="_blank">%s</a>',
                $this->generateUrl('show_application_document', ['fileName' => $value]),
                'Letter of Motivation'
            ));

        switch ($pageName) {
            case Crud::PAGE_INDEX:
                $fields = [
                    AssociationField::new('job'),
                    $applicantField,
                    $applicationStatusTextField,
                    $isArchivedField
                ];
                break;

            case Crud::PAGE_DETAIL:
                $fields = [
                    $applicantField,
                    $cvField,
                    $motivationField
                ];
                break;

            case Crud::PAGE_EDIT:
                $fields = [
                    $applicationStatusAssociationField,
                    $isArchivedField
                ];
                break;
        }

        return $fields;
    }


    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW);
    }

}

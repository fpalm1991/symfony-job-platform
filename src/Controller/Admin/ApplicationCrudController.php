<?php

namespace App\Controller\Admin;

use App\Entity\Application;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ApplicationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Application::class;
    }


    public function configureFields(string $pageName): iterable
    {

        $fields = [
            AssociationField::new('job'),

            AssociationField::new('applicant')
                ->setCrudController(ApplicantCrudController::class)
                ->setLabel('Applicant'),
        ];

        if ($pageName === Crud::PAGE_DETAIL) {
            $fields[] = TextField::new('curriculum_vitae', 'Curriculum Vitae')
                ->formatValue(function ($value, $entity) {
                    return sprintf(
                        '<a href="%s" target="_blank">%s</a>',
                        $this->generateUrl('show_application_document', ['fileName' => $value]),
                        'Curriculum Vitae'
                    );
                });

            $fields[] = TextField::new('letter_of_motivation', 'Letter of Motivation')
                ->formatValue(function ($value, $entity) {
                    return sprintf(
                        '<a href="%s" target="_blank">%s</a>',
                        $this->generateUrl('show_application_document', ['fileName' => $value]),
                        'Letter of Motivation'
                    );
                });
        }

        return  $fields;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW);
    }

}

<?php

namespace App\Controller\Admin;

use App\Entity\Application;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ApplicationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Application::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            // IdField::new('id'),
            // TextField::new('curriculum_vitae', 'Curriculum Vitae'),
            TextField::new('curriculum_vitae', 'Curriculum Vitae')
                ->formatValue(function ($value, $entity) {
                    return sprintf(
                        '<a href="%s" target="_blank">%s</a>',
                        $this->generateUrl('show_application_document', ['fileName' => $value]),
                        'Curriculum Vitae'
                    );
                }),

            TextField::new('letter_of_motivation', 'Letter of Motivation')
                ->formatValue(function ($value, $entity) {
                    return sprintf(
                        '<a href="%s" target="_blank">%s</a>',
                        $this->generateUrl('show_application_document', ['fileName' => $value]),
                        'Letter of Motivation'
                    );
                }),

            AssociationField::new('applicant')->setLabel('Applicant')
                ->formatValue(function ($value, $entity) {
                    return $entity->getApplicant()->getFirstName() . " " . $entity->getApplicant()->getLastName();
                }),
        ];
    }


}

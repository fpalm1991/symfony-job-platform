<?php

namespace App\Controller\Admin;

use App\Entity\Job;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class JobCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Job::class;
    }

    public function configureFields(string $pageName): iterable
    {

        $fields = [
            TextField::new('title'),
            IntegerField::new('fte'),
            ImageField::new('headerImage', 'Header Image for Job')
                ->setUploadDir('public/image/jobs/')
                ->setBasePath('/image/jobs/'),
        ];

        if ($pageName === CRUD::PAGE_NEW) {
            $fields[] = AssociationField::new('features')
                ->setFormTypeOptions([
                    'by_reference' => false,
                ])
                ->formatValue(function ($value, $entity) {
                    return implode(', ', $entity->getFeatures()->map(fn($feature) => $feature->getTitle())->toArray());
                });

            $fields[] = TextAreaField::new('description');
        }

        if ($pageName === CRUD::PAGE_DETAIL OR $pageName === CRUD::PAGE_EDIT) {
            $fields[] = AssociationField::new('features')
                ->setFormTypeOptions([
                    'by_reference' => false,
                ])
                ->formatValue(function ($value, $entity) {
                    return implode(', ', $entity->getFeatures()->map(fn($feature) => $feature->getTitle())->toArray());
                });

            $fields[] = TextAreaField::new('description');
        }

        $fields[] = BooleanField::new('isActive', 'Is Active')
            ->setFormTypeOption('data', true);

        return $fields;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

}

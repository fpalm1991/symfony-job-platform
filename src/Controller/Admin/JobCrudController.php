<?php

namespace App\Controller\Admin;

use App\Entity\Job;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
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
        return [
            // IdField::new('id'),
            TextField::new('title'),
            IntegerField::new('fte'),
            AssociationField::new('features')
                ->setFormTypeOptions([
                    'by_reference' => false,
                ])
                ->formatValue(function ($value, $entity) {
                    return implode(', ', $entity->getFeatures()->map(fn($feature) => $feature->getTitle())->toArray());
                }),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // Enable the "Show" action
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

}

<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $roles = [
            'Applicant' => 'ROLE_APPLICANT',
            'Admin' => 'ROLE_ADMIN',
            'User' => 'ROLE_USER',
        ];

        $fields = [
            IdField::new('id'),
            TextField::new('first_name'),
            TextField::new('last_name'),
            EmailField::new('email'),
            ChoiceField::new('roles')
                ->setChoices($roles)
                ->allowMultipleChoices()
                ->setLabel('User Roles'),
        ];

        if ($pageName === Crud::PAGE_NEW) {
            $fields[] = TextField::new('password')->setLabel('Password');
        }

        return $fields;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        // Filter out users whose roles include ROLE_APPLICANT using a NOT LIKE query
        $qb->andWhere('entity.roles NOT LIKE :role')
            ->setParameter('role', '%ROLE_APPLICANT%');

        return $qb;
    }

}

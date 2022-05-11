<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\LogisticProvider;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class LogisticProviderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LogisticProvider::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Logisticien')
            ->setEntityLabelInPlural('Logisticiens')
            ->showEntityActionsInlined()
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('name'),
            CollectionField::new('warehouses'),
        ];
    }
}

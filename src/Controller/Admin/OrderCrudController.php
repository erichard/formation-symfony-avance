<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Commande')
            ->setEntityLabelInPlural('Commandes')
            ->showEntityActionsInlined()
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPaginatorPageSize(50)
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW, Action::DELETE, Action::EDIT)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield DateTimeField::new('createdAt', 'Date de création');
        yield TextField::new('id');
        yield IntegerField::new('totalQuantity', 'Quantité commandé');
        yield CollectionField::new('items', 'Nombre de produit')
            ->onlyOnIndex();
        yield ArrayField::new('items', false)
            ->onlyOnDetail()
            ->setTemplatePath('admin/order/_items.html.twig');
    }
}

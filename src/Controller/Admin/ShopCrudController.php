<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Shop;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

class ShopCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Shop::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Boutique')
            ->setEntityLabelInPlural('Boutiques')
            ->showEntityActionsInlined()
            ->setDefaultSort(['name' => 'ASC'])
            ->setPaginatorPageSize(50)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name', 'Nom');
        yield UrlField::new('domain', 'Url');
        yield BooleanField::new('enabled', 'Active');

        yield AssociationField::new('brands', 'Marques')->setQueryBuilder(
            fn (QueryBuilder $queryBuilder) => $queryBuilder->addCriteria(Criteria::create()->orderBy(['name' => Criteria::ASC]))
        );
    }
}

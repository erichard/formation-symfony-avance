<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Article')
            ->setEntityLabelInPlural('Articles')
            ->setDefaultSort(['quantityInStock' => 'DESC'])
            ->setSearchFields(['id', 'color', 'products.id', 'brand.name'])
            ->showEntityActionsInlined()
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
        yield FormField::addPanel('Informations principales');
        yield TextField::new('id', 'Reférence');
        yield TextField::new('brand', 'Marque');
        yield TextField::new('title', 'Nom')->onlyOnIndex();
        yield TextField::new('color', 'Couleur');
        yield TextField::new('saison', 'Saison de création')->onlyOnDetail();
        yield ArrayField::new('saisonsCommerciales', 'Saison commerciales')->onlyOnDetail();

        yield FormField::addPanel('Stock & Prix');
        yield IntegerField::new('quantityInStock', 'Stock')->setTextAlign('right');
        yield MoneyField::new('minPrixVente', 'Prix de vente à partir de')
            ->setCurrency('EUR')->setTextAlign('right');
        yield MoneyField::new('minPrixAchat', 'Prix d\'achat à partir de')
            ->setCurrency('EUR')->setTextAlign('right');

        yield FormField::addPanel('Produits');
        yield ArrayField::new('products', false)
            ->onlyOnDetail()
            ->setTemplatePath('admin/article/_products.html.twig')
        ;

        yield FormField::addPanel('Caractéristiques');
        yield TextField::new('fabrication', 'Fabrication')->onlyOnDetail();
        yield TextField::new('madeInCountry', 'Pays de fabrication')->onlyOnDetail();
        yield TextField::new('ligne', 'Ligne')->onlyOnDetail();
        yield TextField::new('genre', 'Genre')->onlyOnDetail();
        yield TextField::new('fermeture', 'Fermeture')->onlyOnDetail();
        yield TextField::new('semelle', 'Semelle')->onlyOnDetail();
        yield ArrayField::new('tige', 'Tige')->onlyOnDetail();
        yield ArrayField::new('doublure', 'Doublure')->onlyOnDetail();
        yield TextField::new('typeTalon', 'Type Talon')->onlyOnDetail();
        yield NumberField::new('hauteurTalon', 'Hauteur Talon')->onlyOnDetail();
        yield NumberField::new('hauteurPlateforme', 'Hauteur Plateforme')->onlyOnDetail();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('brand')
            ->add('title')
            ->add('color')
            ->add('quantityInStock')
            ->add('minPrixAchat')
            ->add('minPrixVente')
        ;
    }
}

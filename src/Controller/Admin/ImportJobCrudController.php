<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ImportJob;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Khill\Duration\Duration;

class ImportJobCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ImportJob::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Import')
            ->setEntityLabelInPlural('Imports')
            ->setSearchFields(['title'])
            ->setPageTitle('detail', fn ($product) => $product->getTitle())
            ->showEntityActionsInlined()
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id');
        yield DateTimeField::new('createdAt', 'Date de lancement');
        yield DateTimeField::new('finishedAt', 'Date de fin')
            ->onlyOnDetail();
        yield TextField::new('filename', 'Fichier')
            ->formatValue(fn ($value) => '<code>'.basename($value).'</code>')
            ->onlyOnDetail();
        yield TextField::new('status', 'Statut')
            ->setTextAlign('center')
            ->formatValue(function ($value) {
                if ('started' === $value) {
                    return '<span class="badge bg-primary text-light"><i class="fa fa-spin fa-spinner"></i> En cours&hellip;</span>';
                } elseif ('completed' === $value) {
                    return '<span class="badge bg-success text-light">Complété</span>';
                } elseif ('completed_with_errors' === $value) {
                    return '<span class="badge bg-warning text-light">Partiel</span>';
                }
            });
        yield TextField::new('title', 'Titre')
            ->onlyOnIndex();
        yield IntegerField::new('duration', "Durée d'exécution")
            ->setTextAlign('right')
            ->formatValue(fn ($value) => null === $value ? '-' : (new Duration($value))->humanize());
        yield NumberField::new('importedItemCount', 'Elements importés')
            ->setTextAlign('right')
            ->formatValue(fn ($value) => $value <= 0 ? '-' : $value);
        yield NumberField::new('errorCount', 'Erreurs')
            ->setTextAlign('right')
            ->formatValue(fn ($value) => $value <= 0 ? '-' : $value);
        yield ArrayField::new('errors', false)
            ->onlyOnDetail()
            ->setTemplatePath('admin/import_job/_errors.html.twig')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW, Action::DELETE, Action::EDIT)
        ;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $qb->select('partial entity.{id, createdAt, status, title, errorCount, importedItemCount, duration}');

        return $qb;
    }
}

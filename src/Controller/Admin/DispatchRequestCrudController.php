<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\Field\JsonField;
use App\Admin\Field\StatusField;
use App\Entity\DispatchRequest;
use App\Orliweb\StockImporter;
use App\Repository\DispatchRequestRepository;
use App\Service\Dispatcher;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class DispatchRequestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DispatchRequest::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Routage')
            ->setEntityLabelInPlural('Routages')
            ->showEntityActionsInlined()
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPaginatorPageSize(50)
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $retry = Action::new('Retry', 'Relancer', 'fa fa-redo')
            ->linkToCrudAction('retryDispatch');

        return $actions
            ->add(Crud::PAGE_DETAIL, $retry)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW, Action::DELETE, Action::EDIT)
        ;
    }

    public function retryDispatch(AdminContext $context, Dispatcher $dispatcher, DispatchRequestRepository $repository, StockImporter $orliStock, AdminUrlGenerator $adminUrlGenerator)
    {
        $dispatchRequest = $context->getEntity()->getInstance();

        $cloned = clone $dispatchRequest;

        $dispatcher->dispatch($cloned);

        // Refresh des stocks si dispatch sur Orliweb
        if ($cloned->isDispatchedFromOrliweb()) {
            $dispatchRequest->markDecisionPassed('Dispatch depuis Orliweb detecté, actualisation des stocks et redispatch');
            $orliStock->importFromAPI($cloned->getItemsEANs());
            $dispatcher->dispatch($cloned);
        }

        $repository->finishDispatch($cloned);

        $url = $adminUrlGenerator
            ->setAction(Action::DETAIL)
            ->setEntityId($cloned->getId())
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->hideOnForm();
        yield DateTimeField::new('createdAt', 'Date de demande')
            ->hideOnForm();
        yield IntegerField::new('quantity', 'Quantité demandée')
            ->hideOnForm();
        yield JsonField::new('items', 'Produits')
            ->onlyOnDetail();
        yield JsonField::new('stocks', 'Stocks')
            ->setHelp('Etat de stocks des produits au moment du dispatch')
            ->onlyOnDetail();
        yield JsonField::new('result', 'Retour')
            ->onlyOnDetail();
        yield JsonField::new('PTResult', 'Retour PT')
            ->setHelp('Retour calculé par la plateforme tampon')
            ->onlyOnDetail();
        yield TextField::new('formattedTimings', 'Timings OMS <small class="text-muted"> -> PT </small>')
            ->hideOnForm()
            ->renderAsHtml()
            ->setHelp('Temps d\'exécution');
        yield StatusField::new('valid', 'Conforme PT')
            ->hideOnForm()
            ->setTextAlign('center');
        yield ArrayField::new('decisionLog', 'Journal de décision')
            ->onlyOnDetail()
            ->setTemplatePath('admin/dispatch_request/_log.html.twig');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('createdAt')
            ->add('valid')
        ;
    }
}

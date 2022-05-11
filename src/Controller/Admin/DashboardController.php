<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Carrier;
use App\Entity\DispatchRequest;
use App\Entity\ImportJob;
use App\Entity\LogisticProvider;
use App\Entity\Order;
use App\Entity\Shop;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardController extends AbstractDashboardController
{
    #[Route('/', name: 'admin')]
    public function index(): Response
    {
        return $this->render('dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Formation SF Avancé')
            ->setFaviconPath('favicon.ico');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('OMS');
        yield MenuItem::linkToCrud('Routage Panier', 'fa fa-random', DispatchRequest::class);
        yield MenuItem::linkToCrud('Commandes', 'fa fa-shopping-cart', Order::class);

        yield MenuItem::section('Référentiels');
        yield MenuItem::linkToCrud('Logisticiens', 'fa fa-cogs', LogisticProvider::class);
        yield MenuItem::linkToCrud('Transporteurs', 'fa fa-truck', Carrier::class);
        yield MenuItem::linkToCrud('Articles', 'fa fa-shoe-prints', Article::class);
        yield MenuItem::linkToCrud('Boutiques', 'fa fa-store', Shop::class);

        yield MenuItem::section('Tâches de fond');
        yield MenuItem::linkToCrud('Imports', 'fa fa-file-import', ImportJob::class);

        yield MenuItem::section('Formation');
        yield MenuItem::linkToRoute('Controller lent', 'fa fa-file-import', 'app_slow');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->displayUserName(false)
        ;
    }
}

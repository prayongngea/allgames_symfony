<?php

namespace App\Controller\Admin;

use App\Entity\Editor;
use App\Entity\Game;
use App\Entity\Genre;
use App\Entity\Review;
use App\Entity\User;
use App\Entity\WishlistItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('AllGames Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Editor', 'fas fa-building', Editor::class);
        yield MenuItem::linkToCrud('Game', 'fas fa-gamepad', Game::class);
        yield MenuItem::linkToCrud('Genre', 'fas fa-tags', Genre::class);
        yield MenuItem::linkToCrud('User', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('WishlistItem', 'fas fa-heart', WishlistItem::class);
        yield MenuItem::linkToCrud('Review', 'fas fa-star', Review::class);
    }
}

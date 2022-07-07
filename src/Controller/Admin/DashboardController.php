<?php

namespace App\Controller\Admin;

use App\Entity\Actor;
use App\Entity\Director;
use App\Entity\Film;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin/{object}", name="admin")
     */
    public function index(string $object = ''): Response
    {
        // redirect to some CRUD controller
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        $controllers = ['film' => FilmCrudController::class, 'actor' => ActorCrudController::class, 'director' => DirectorCrudController::class];
        $selectedController = array_key_exists($object, $controllers) ? $controllers[$object] : null;

        return $selectedController ? $this->redirect($adminUrlGenerator->setController($selectedController)->generateUrl()) : parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Prueba');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Objects');
        yield MenuItem::linkToCrud('Films', 'fa fa-list', Film::class);
        yield MenuItem::linkToCrud('Actors', 'fa fa-user', Actor::class);
        yield MenuItem::linkToCrud('Directors', 'fa fa-user', Director::class);
    }
}

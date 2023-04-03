<?php

namespace App\Controller\Admin;

use App\Entity\Ticket;
use App\Entity\TicketTypology;
use App\Repository\TicketRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
     public function __construct(private TicketRepository $tickets)
    {
    } 


    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        //return parent::index();
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        //return $this->redirect($adminUrlGenerator->setController(TicketCrudController::class)->generateUrl());

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        return $this->render('easy_admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('TickPass');
    }

    public function configureMenuItems(): iterable
    {
        $numOfTickets = $this->tickets->getCount();

        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Ticket Management', 'fa-solid fa-qrcode');
        yield MenuItem::linkToCrud('Generate ticket', 'fas fa-square-plus', Ticket::class)->setAction('new');
        yield MenuItem::linkToCrud('List tickets', 'fas fa-ticket', Ticket::class)->setBadge($numOfTickets);

        yield MenuItem::section('Settings', 'fas fa-gear');

        yield MenuItem::linkToCrud('Ticket types', 'fas fa-list', TicketTypology::class);

        yield MenuItem::section('Info');
        yield MenuItem::linkToUrl('Repository', 'fa-brands fa-github', 'https://github.com/franjsco/TickPass');


    }
}

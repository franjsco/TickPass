<?php

namespace App\Controller\Admin;

use App\Entity\TicketTypology;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TicketTypologyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TicketTypology::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}

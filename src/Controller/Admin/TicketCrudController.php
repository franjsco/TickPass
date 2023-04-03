<?php

namespace App\Controller\Admin;

use App\EasyAdmin\Field\DownloadField;
use App\Entity\Ticket;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{IdField, TextField, DateTimeField, AssociationField, FormField};
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use \EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Dompdf\Dompdf;
use chillerlan\QRCode\{QRCode, QROptions};
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;


class TicketCrudController extends AbstractCrudController
{
    public function __construct(
        private ManagerRegistry $doctrine
    ){}

    public static function getEntityFqcn(): string
    {
        return Ticket::class;
    }


    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('serial')->hideOnForm();
        yield AssociationField::new('typology');
        yield TextField::new('note');
        yield TextField::new('status')->hideOnForm();
        yield DateTimeField::new('generation_date')->hideOnForm();
        yield DateTimeField::new('validation_date')->hideOnForm()->hideOnIndex();
        yield DateTimeField::new('deactivation_date')->hideOnForm()->hideOnIndex();
        
        if (Crud::PAGE_DETAIL === $pageName) {
            yield FormField::addPanel('QR Pass')->setIcon('fa-solid fa-qrcode');
            yield DownloadField::new('Download');
        }
        
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDateTimeFormat('yyyy-MM-dd HH:mm:ss')
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'List Ticket')
            ->setPageTitle('new', 'Generate Ticket')
            ->setSearchFields(['note', 'serial'])
            ->setPaginatorPageSize(12)
            ->setDefaultSort(['id' => 'DESC']);
    }
    


    public function configureActions(Actions $actions): Actions
    {
        
        $setTicketAsDeactivate = Action::new('setTicketAsDeactivate', 'Set As Deactivate')
            ->setCssClass('btn btn-danger')
            ->setIcon('fas fa-ban')
            ->displayIf(static function ($entity) {
                return !$entity->isDeactivate();
            })
            ->linkToCrudAction('setTicketAsDeactivate'); 

        $setTicketAsValidated = Action::new('setTicketAsValidated', 'Set As Validated')
        ->setCssClass('btn btn-warning')
        ->setIcon('fas fa-check')
        ->displayIf(static function ($entity) {
            return !$entity->isValidated();
        })
        ->linkToCrudAction('setTicketAsValidated'); 


        $setTicketAsValid = Action::new('setTicketAsValid', 'Set As Valid')
        ->setCssClass('btn btn-success')
        ->setIcon('fas fa-check')
        ->displayIf(static function ($entity) {
            return !$entity->isValid();
        })
        ->linkToCrudAction('setTicketAsValid'); 



        return $actions
            // Actions for index
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_DETAIL, $setTicketAsDeactivate)
            ->add(Crud::PAGE_DETAIL, $setTicketAsValidated)
            ->add(Crud::PAGE_DETAIL, $setTicketAsValid)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::NEW)

            
            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
                return $action->setIcon('fa fa-file-alt');
            })

            /*
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setIcon('fas fa-pen-to-square');
            }) */

            // Actions for detail
            ->disable(Action::DELETE)
            ->disable(Action::EDIT)
            //->remove(Crud::PAGE_DETAIL, Action::EDIT)


            // Actions for edit
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)

            // Actions for new
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
            ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('status')
            ->add('typology')
            ->add('generation_date');
    }


    public function setTicketAsDeactivate()
    {
        $ticket = $this->getContext()->getEntity()->getInstance();
        $ticket->setAsDeactive();
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($ticket);

        $entityManager->flush();

        return $this->redirect($this->getContext()->getReferrer());
    }

    public function setTicketAsValid()
    {
        $ticket = $this->getContext()->getEntity()->getInstance();
        $ticket->setAsValid();
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($ticket);

        $entityManager->flush();

        return $this->redirect($this->getContext()->getReferrer());
    }

    public function setTicketAsValidated()
    {
        $ticket = $this->getContext()->getEntity()->getInstance();
        $ticket->setAsValidated();
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($ticket);

        $entityManager->flush();

        return $this->redirect($this->getContext()->getReferrer());
    }

    public function downloadPass()
    {
        $ticket = $this->getContext()->getEntity()->getInstance();

        $path = $this->getParameter('app.data_dir');
        $filename = 'PASS_' . $ticket->getSerial() . '.pdf';

        $response = new BinaryFileResponse("$path/$filename");

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $filename
        );

        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }
}

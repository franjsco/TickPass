<?php

namespace App\EventSubscriber;

use App\Entity\Ticket;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use chillerlan\QRCode\{QRCode, QROptions};
use Symfony\Component\Filesystem\Filesystem;
use Dompdf\Dompdf;
use Twig\Environment;


class EasyAdminSubscriber implements EventSubscriberInterface
{   
    public function __construct(
        private Environment $twig,
        private ContainerBagInterface $params
    ) {}

    public static function getSubscribedEvents() {
        return [
            AfterEntityPersistedEvent::class => 'generatePass',
        ];
    }


    public function generatePass(AfterEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Ticket)) {
            return;
        }

        $options = new QROptions([
            'imageTransparent' => false,
            'scale' => 12
        ]);

        $dompdf = new Dompdf();
        
        $html = $this->twig->render('pdf/pass.html.twig', [ 
            "serial" => $entity->getSerial(), 
            "typology" => $entity->getTypology(),
            "note" => $entity->getNote(),
            "qrcodedata" => (new QRCode($options))->render($entity->getSerial())
        ]);

        $dompdf->loadHtml($html);
        $dompdf->render();
        $output = $dompdf->output();

        $path = $this->params->get('app.data_dir');
        $filename = 'PASS_' . $entity->getSerial() . '.pdf';

        $fs = new Filesystem();

        $fs->dumpFile("$path/$filename", $output);
    }
}
<?php

namespace App\Controller;

use App\Entity\Ticket;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class TicketValidatorController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('ticket_validator/index.html.twig');
    }

    public function checkSerial(Request $request, ManagerRegistry $doctrine, SerializerInterface $serializer): Response
    {
        $body = json_decode($request->getContent(), true);;

        if (!array_key_exists('serial', $body)) {
            return $this->json(['message' => 'serial parameter not found'], 400);
        }

        
        $ticket = $doctrine->getRepository(Ticket::class)->findOneBy(["serial" => $body['serial']]);

        if (!$ticket) {
            return $this->json(['message' => 'ticket non found'], 404);
        }
        
        if ($ticket->getStatus() == 'valid') {
            $ticket->setValidationDate(new DateTime());
        
            $ticketClone = clone $ticket;
            $ticket->setStatus('validated');
            $entityManager = $doctrine->getManager();
            $entityManager->persist($ticket);
            $entityManager->flush();

            //TODO: fix avoid duplication
            $json = $serializer->serialize(
                $ticketClone,
                JsonEncoder::FORMAT,
                [DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s']
            );

            return new Response($json);
        }
        
        $json = $serializer->serialize(
            $ticket,
            JsonEncoder::FORMAT,
            [DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s']
        );

        return new Response($json);
    }
}

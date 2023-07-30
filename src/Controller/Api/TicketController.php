<?php

namespace App\Controller\Api;

use App\DTO\Request\TicketResponseDTO;
use App\Service\TicketService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/api/ticket')]
#[IsGranted('ROLE_SUPPORT')]
class TicketController extends AbstractController
{
    public function __construct(
        private readonly TicketService $ticketService,
    )
    {
    }

    #[Route(path: '/take', methods: ['GET'])]
    public function takeNewTicket(): Response {
        $ticket = $this->ticketService->takeNewTicket();

        return $this->json(TicketResponseDTO::fromEntity($ticket), $ticket ? Response::HTTP_OK : Response::HTTP_NO_CONTENT);
    }
}
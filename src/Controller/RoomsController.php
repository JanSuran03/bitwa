<?php

namespace App\Controller;

use App\Service\RoomService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RoomsController extends AbstractController {

    private RoomService $roomService;

    public function __construct(RoomService $roomService) {
         $this->roomService = $roomService;
    }

    #[Route('/rooms', name: 'app_rooms')]
    public function rooms(Request $request): Response {
        $searchQuery = $request->query->get('search');
        $rooms = $this->roomService->getAllByName($searchQuery);
        $currentAvailabilityMap = $this->roomService->getCurrentAvailabilityMap($rooms);

        return $this->render(
            'rooms.html.twig',
            [
                'rooms' => $rooms,
                'currentAvailabilityMap' => $currentAvailabilityMap,
            ]
        );
    }

    #[Route('/rooms/{roomUrlName}/book', name: 'app_room_book')]
    public function booking(Request $request): Response {
        return $this->render('reservation.html.twig');
    }
}

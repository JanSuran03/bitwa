<?php

namespace App\Controller;

use App\Service\RoomService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

    #[Route('/rooms/new', name: 'app_create_room', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function createRoom(Request $request): ?Response {
        $roomName = $request->request->get('_room_name');
        $buildingName = $request->request->get('_building_name');
        $isPublic = $request->request->get('_is_public') === 1;

        if ($this->roomService->findByNameAndBuilding($roomName, $buildingName) === null) {
            // ignore db for now
            $this->addFlash('success', 'Room created successfully');
            return $this->redirectToRoute('app_rooms');
        } else {
            $this->addFlash('error', 'Room with the same name already exists.');

            $rooms = $this->roomService->getAll(); // TODO: query?
            $currentAvailabilityMap = $this->roomService->getCurrentAvailabilityMap($rooms);
            return $this->render('rooms.html.twig',
                [
                    'previous_input' => [
                        'room_name' => $roomName,
                        'building_name' => $buildingName,
                        'is_public' => $isPublic
                    ],
                    'rooms' => $rooms,
                    'currentAvailabilityMap' => $currentAvailabilityMap,
                ]);
        }
    }
}

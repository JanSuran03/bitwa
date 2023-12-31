<?php

namespace App\Controller;

use App\Entity\User;
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
        /** @var User $user */
        $user = $this->getUser();

        $searchQuery = $request->query->get('search');
        $rooms = $this->roomService->getAllByName($searchQuery);
        $bookableRooms = ($user === null) ? [] : $this->roomService->getAllBookableBy($user);
        $manageableRooms = ($user === null) ? [] : $this->roomService->getAllManageableBy($user);
        $currentAvailabilityMap = $this->roomService->getCurrentAvailabilityMap($rooms);

        return $this->render(
            'rooms.html.twig',
            [
                'rooms' => $rooms,
                'bookableRooms' => $bookableRooms,
                'manageableRooms' => $manageableRooms,
                'currentAvailabilityMap' => $currentAvailabilityMap,
            ]
        );
    }

    #[Route('/rooms/{id}', name: 'app_room')]
    public function room(Request $request, int $id): Response {
        return $this->render('room.html.twig');
    }

    #[Route('/rooms/new', name: 'app_create_room', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function createRoom(Request $request): ?Response {
        $roomName = $request->request->get('_room_name');
        $buildingName = $request->request->get('_building_name');
        $isPublic = $request->request->has('_is_public') ? 1 : 0;

        if (empty($roomName) || empty($buildingName)) {
            $this->addFlash('error', 'Vyplňte budovu a místnost.');
            return $this->redirectToRoute('app_rooms');
        } else if ($this->roomService->findByNameAndBuilding($roomName, $buildingName) === null) {
            $this->roomService->createRoom($roomName, $buildingName, $isPublic === 1);

            $this->addFlash('success', 'Místnost úspěšně vytvořena.');
            return $this->redirectToRoute('app_rooms');
        } else {
            $this->addFlash('error', 'Místnost s touto budovou a jménem již existuje.');

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

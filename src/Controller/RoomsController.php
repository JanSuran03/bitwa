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
            /** @var User $user */
            $user = $this->getUser();
            $bookableRooms = $this->roomService->getAllBookableBy($user);
            $manageableRooms = $this->roomService->getAllManageableBy($user);

            return $this->render('rooms.html.twig',
                [
                    'previous_input' => [
                        'room_name' => $roomName,
                        'building_name' => $buildingName,
                        'is_public' => $isPublic
                    ],
                    'rooms' => $rooms,
                    'currentAvailabilityMap' => $currentAvailabilityMap,
                    'bookableRooms' => $bookableRooms,
                    'manageableRooms' => $manageableRooms,
                ]);
        }
    }

    #[Route('/rooms/{id}', name: 'app_room')]
    public function room(Request $request, int $id): Response {
        $room = $this->roomService->getOneById($id);
        /** @var User $user */
        $user = $this->getUser();
        if ($room == null) {
            $this->addFlash('error', 'Místnost s identifikátorem ' . $id . ' nebyla nalezena.');
            return $this->redirectToRoute('app_rooms');
        } else if ($user == null && !$room->isPublic()) {
            $this->addFlash('error', 'Neoprávněný přístup k místnosti s identifikátorem ' . $id . ' - prosíme, přihlaste se.');
            return $this->redirectToRoute('app_rooms');
        }

        return $this->render('room.html.twig',
            [
                'room' => $room,
                'is_manageable' => $user !== null && $this->roomService->isTransitiveManagerOf($user, $room),
                'is_bookable' => $user !== null && $this->roomService->isBookableBy($room, $user),
                'is_occupied' => $this->roomService->isOccupiedNow($room)]);
    }

    #[Route('/rooms/{id}/change-room', name: 'app_room_change_room', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function changeRoomName(Request $request, int $id): Response {
        $newName = $request->request->get('_room');
        if (empty($newName)) {
            $this->addFlash('error', 'Název místnosti nemůže být prázdné.');
            return $this->redirectToRoute('app_room', ['id' => $id]);
        }

        $room = $this->roomService->getOneById($id);
        if ($room == null) {
            $this->addFlash('error', 'Špatný požadavek, místnost s identifikátorem ' . $id . ' neexistuje.');
            return $this->redirectToRoute('app_rooms');
        }

        if ($this->roomService->findByNameAndBuilding($newName, $room->getBuilding()) != null) {
            $this->addFlash('error', 'Místnost s touto budovou a jménem již existuje.');
            return $this->redirectToRoute('app_room', ['id' => $id]);
        }

        $room->setName($newName);
        $this->roomService->setRoom($room);
        $this->addFlash('success', 'Název místnosti změněn.');
        return $this->redirectToRoute('app_room', ['id' => $id]);
    }

    #[Route('/rooms/{id}/change-building', name: 'app_room_change_building', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function changeRoomBuilding(Request $request, int $id): Response {
        $newBuilding = $request->request->get('_building');
        if (empty($newBuilding)) {
            $this->addFlash('error', 'Název budovy nemůže být prázdné.');
            return $this->redirectToRoute('app_room', ['id' => $id]);
        }

        $room = $this->roomService->getOneById($id);
        if ($room == null) {
            $this->addFlash('error', 'Špatný požadavek, místnost s identifikátorem ' . $id . ' neexistuje.');
            return $this->redirectToRoute('app_rooms');
        }

        if ($this->roomService->findByNameAndBuilding($room->getName(), $newBuilding) != null) {
            $this->addFlash('error', 'Místnost s touto budovou a jménem již existuje.');
            return $this->redirectToRoute('app_room', ['id' => $id]);
        }

        $room->setBuilding($newBuilding);
        $this->roomService->setRoom($room);
        $this->addFlash('success', 'Název budovy změněn.');
        return $this->redirectToRoute('app_room', ['id' => $id]);
    }

    #[Route('/rooms/{id}/change-availability-for-the-public', name: 'app_room_change_availability_for_the_public', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function changeAvailabilityForThePublic(Request $request, int $id): Response {
        $newIsPublic = $request->request->has('_is_public') ? 1 : 0;
        $room = $this->roomService->getOneById($id);

        if ($room == null) {
            $this->addFlash('error', 'Špatný požadavek, místnost s identifikátorem ' . $id . ' neexistuje.');
            return $this->redirectToRoute('app_rooms');
        }

        $room->setPublic($newIsPublic);
        $this->roomService->setRoom($room);
        $this->addFlash('success', ' Přístupnost pro veřejnost změněna.');
        return $this->redirectToRoute('app_room', ['id' => $id]);
    }

}

<?php

namespace App\Api\Controller;

use App\Api\DTO\RoomResponse;
use App\Entity\Room;
use App\Service\RoomService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Request;

class RoomApiController extends AbstractFOSRestController
{
    private RoomService $roomService;

    public function __construct(RoomService $roomService)
    {
        $this->roomService = $roomService;
    }

    #[View]
    #[Get("/rooms")]
    public function list(Request $request): array
    {
        $rooms = $this->roomService->getAllByApiQueries($request->query->all());
        return array_map(
            fn(Room $room) => RoomResponse::fromEntity($room),
            $rooms
        );
    }

    #[View]
    #[Get("/rooms/{roomId}/allowed-users/{userId}")]
    public function checkIfAllowed(int $roomId, int $userId): bool
    {
        return $this->roomService->isAllowed($roomId, $userId);
    }
}
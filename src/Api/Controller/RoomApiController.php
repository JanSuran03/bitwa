<?php

namespace App\Api\Controller;

use App\Api\DTO\LockResponse;
use App\Api\DTO\RoomResponse;
use App\Entity\Room;
use App\Service\RoomService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RoomApiController extends AbstractFOSRestController {
    private RoomService $roomService;

    public function __construct(RoomService $roomService) {
        $this->roomService = $roomService;
    }

    #[View]
    #[Get("/rooms")]
    public function list(Request $request): array {
        $rooms = $this->roomService->getAllByApiQueries($request->query->all());
        return array_map(
            fn(Room $room) => RoomResponse::fromEntity($room),
            $rooms
        );
    }

    #[View]
    #[ParamConverter('room', class: 'App\Entity\Room')]
    #[Get("/rooms/{id}")]
    public function detail(Room $room): RoomResponse {
        return RoomResponse::fromEntity($room);
    }

    #[View]
    #[Get("/rooms/{roomId}/allowed-users/{userId}")]
    public function checkIfAllowed(int $roomId, int $userId): bool {
        return $this->roomService->isAllowed($roomId, $userId);
    }

    #[View]
    #[Put("/rooms/{roomId}/double-tap")]
    public function doubleTap(int $roomId, Request $request): LockResponse {
        $userId = $request->query->get('user');
        if (!$userId) {
            throw new BadRequestHttpException('Missing mandatory query parameter user');
        }
        return $this->roomService->doubleTapById($roomId, $userId);
    }

    #[View]
    #[Delete("/rooms/{id}")]
    public function delete(int $id): Response {
        $this->roomService->deleteById($id);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
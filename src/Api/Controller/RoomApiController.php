<?php

namespace App\Api\Controller;

use App\Api\DTO\GroupResponse;
use App\Api\DTO\LockResponse;
use App\Api\DTO\RoomResponse;
use App\Api\DTO\UserResponse;
use App\Entity\Group;
use App\Entity\Room;
use App\Entity\User;
use App\Service\RoomService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
    #[ParamConverter('room', class: 'App\Entity\Room')]
    #[Get("/rooms/{id}")]
    public function detail(Room $room): RoomResponse
    {
        return RoomResponse::fromEntity($room);
    }

    #[View]
    #[ParamConverter('room', class: 'App\Entity\Room')]
    #[Get("/rooms/{id}/managers")]
    public function managersList(Room $room): array
    {
        return array_map(
            fn(User $user) => UserResponse::fromEntity($user),
            $room->getManagers()->toArray()
        );
    }

    #[View]
    #[Get("/rooms/{roomId}/allowed-users/{userId}")]
    public function checkIfAllowed(int $roomId, int $userId): bool
    {
        return $this->roomService->isAllowed($roomId, $userId);
    }

    #[View]
    #[Put("/rooms/{roomId}/double-tap")]
    public function doubleTap(int $roomId, Request $request): LockResponse
    {
        $userId = $request->query->get('user');
        if (!$userId) {
            throw new BadRequestHttpException('Missing mandatory query parameter user');
        }
        return $this->roomService->doubleTapById($roomId, $userId);
    }

    #[View]
    #[ParamConverter('room', class: 'App\Entity\Room', options: ['mapping' => ['roomId' => 'id']])]
    #[ParamConverter('member', class: 'App\Entity\User', options: ['mapping' => ['userId' => 'id']])]
    #[Put("/rooms/{roomId}/members/{userId}")]
    public function addMember(Room $room, User $member): RoomResponse
    {
        $updatedRoom = $this->roomService->addMember($room, $member);
        return RoomResponse::fromEntity($updatedRoom);
    }

    #[View]
    #[ParamConverter('room', class: 'App\Entity\Room', options: ['mapping' => ['roomId' => 'id']])]
    #[ParamConverter('member', class: 'App\Entity\User', options: ['mapping' => ['userId' => 'id']])]
    #[Delete("/rooms/{roomId}/members/{userId}")]
    public function removeMember(Room $room, User $member): void
    {
        $this->roomService->removeMember($room, $member);
    }

    #[View]
    #[ParamConverter('room', class: 'App\Entity\Room', options: ['mapping' => ['roomId' => 'id']])]
    #[ParamConverter('manager', class: 'App\Entity\User', options: ['mapping' => ['userId' => 'id']])]
    #[Put("/rooms/{roomId}/managers/{userId}")]
    public function addManager(Room $room, User $manager): RoomResponse
    {
        $updatedRoom = $this->roomService->addManager($room, $manager);
        return RoomResponse::fromEntity($updatedRoom);
    }

    #[View]
    #[ParamConverter('room', class: 'App\Entity\Room', options: ['mapping' => ['roomId' => 'id']])]
    #[ParamConverter('manager', class: 'App\Entity\User', options: ['mapping' => ['userId' => 'id']])]
    #[Delete("/rooms/{roomId}/managers/{userId}")]
    public function removeManager(Room $room, User $manager): void
    {
        $this->roomService->removeManager($room, $manager);
    }

    #[View]
    #[Delete("/rooms/{id}")]
    public function delete(int $id): void
    {
        $this->roomService->deleteById($id);
    }
}
<?php

namespace App\Api\Controller;

use App\Api\DTO\GroupResponse;
use App\Api\DTO\UserResponse;
use App\Entity\Group;
use App\Entity\Room;
use App\Entity\User;
use App\Service\GroupService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class GroupApiController extends AbstractFOSRestController
{
    private GroupService $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    #[View]
    #[Get("/groups")]
    public function list(Request $request): array
    {
        $groups = $this->groupService->getAllByApiQueries($request->query->all());
        return array_map(
            fn(Group $group) => GroupResponse::fromEntity($group),
            $groups
        );
    }

    #[View]
    #[ParamConverter('group', class: 'App\Entity\Group')]
    #[Get("/groups/{id}")]
    public function detail(Group $group): GroupResponse
    {
        return GroupResponse::fromEntity($group);
    }

    #[View]
    #[ParamConverter('group', class: 'App\Entity\Group')]
    #[Get("/group/{id}/members")]
    public function membersList(Group $group): array
    {
        return array_map(
            fn(User $user) => UserResponse::fromEntity($user),
            $group->getMembers()->toArray()
        );
    }

    #[View]
    #[ParamConverter('group', class: 'App\Entity\Group')]
    #[Get("/group/{id}/managers")]
    public function managersList(Group $group): array
    {
        return array_map(
            fn(User $user) => UserResponse::fromEntity($user),
            $group->getManagers()->toArray()
        );
    }

    #[View]
    #[ParamConverter('group', class: 'App\Entity\Group', options: ['mapping' => ['groupId' => 'id']])]
    #[ParamConverter('member', class: 'App\Entity\User', options: ['mapping' => ['userId' => 'id']])]
    #[Put("/groups/{groupId}/members/{userId}")]
    public function addMember(Group $group, User $member): GroupResponse
    {
        $updatedGroup = $this->groupService->addMember($group, $member);
        return GroupResponse::fromEntity($updatedGroup);
    }

    #[View]
    #[ParamConverter('group', class: 'App\Entity\Group', options: ['mapping' => ['groupId' => 'id']])]
    #[ParamConverter('member', class: 'App\Entity\User', options: ['mapping' => ['userId' => 'id']])]
    #[Delete("/groups/{groupId}/members/{userId}")]
    public function removeMember(Group $group, User $member): void
    {
        $this->groupService->removeMember($group, $member);
    }

    #[View]
    #[ParamConverter('group', class: 'App\Entity\Group', options: ['mapping' => ['groupId' => 'id']])]
    #[ParamConverter('manager', class: 'App\Entity\User', options: ['mapping' => ['userId' => 'id']])]
    #[Put("/groups/{groupId}/managers/{userId}")]
    public function addManager(Group $group, User $manager): GroupResponse
    {
        $updatedGroup = $this->groupService->addManager($group, $manager);
        return GroupResponse::fromEntity($updatedGroup);
    }

    #[View]
    #[ParamConverter('group', class: 'App\Entity\Group', options: ['mapping' => ['groupId' => 'id']])]
    #[ParamConverter('manager', class: 'App\Entity\User', options: ['mapping' => ['userId' => 'id']])]
    #[Delete("/groups/{groupId}/managers/{userId}")]
    public function removeManager(Group $group, User $manager): void
    {
        $this->groupService->removeManager($group, $manager);
    }

    #[View]
    #[ParamConverter('group', class: 'App\Entity\Group', options: ['mapping' => ['groupId' => 'id']])]
    #[ParamConverter('room', class: 'App\Entity\Room', options: ['mapping' => ['roomId' => 'id']])]
    #[Put("/groups/{groupId}/rooms/{roomId}")]
    public function addRoom(Group $group, Room $room): GroupResponse
    {
        $updatedGroup = $this->groupService->addRoom($group, $room);
        return GroupResponse::fromEntity($updatedGroup);
    }

    #[View]
    #[ParamConverter('group', class: 'App\Entity\Group', options: ['mapping' => ['groupId' => 'id']])]
    #[ParamConverter('room', class: 'App\Entity\Room', options: ['mapping' => ['roomId' => 'id']])]
    #[Delete("/groups/{groupId}/rooms/{roomId}")]
    public function removeRoom(Group $group, Room $room): void
    {
        $this->groupService->removeRoom($group, $room);
    }

    #[View]
    #[Delete("/groups/{id}")]
    public function delete(int $id): void
    {
        $this->groupService->deleteById($id);
    }
}
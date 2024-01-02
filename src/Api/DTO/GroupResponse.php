<?php

namespace App\Api\DTO;

use App\Entity\Group;
use App\Entity\Room;
use App\Entity\User;

class GroupResponse
{
    public function __construct(
        public int    $id,
        public array  $members_ids,
        public array  $managers_ids,
        public ?int   $parent_id,
        public array  $child_groups_ids,
        public array  $rooms,
        public string $name,
    )
    {
    }

    public static function fromEntity(Group $entity): self
    {
        return new self(
            $entity->getId(),
            array_map(
                fn(User $user) => $user->getId(),
                $entity->getMembers()->toArray()
            ),
            array_map(
                fn(User $user) => $user->getId(),
                $entity->getManagers()->toArray()
            ),
            $entity->getParent()?->getId(),
            array_map(
                fn(Group $group) => $group->getId(),
                $entity->getChildGroups()->toArray()
            ),
            array_map(
                fn(Room $room) => RoomResponse::fromEntity($room),
                $entity->getRooms()->toArray()
            ),
            $entity->getName(),
        );
    }
}
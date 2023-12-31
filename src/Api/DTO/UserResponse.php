<?php

namespace App\Api\DTO;

use App\Entity\Group;
use App\Entity\Room;
use App\Entity\User;

class UserResponse
{
    public function __construct(
        public int    $id,
        public array  $group_memberships_ids,
        public array  $room_memberships_ids,
        public array  $managed_rooms_ids,
        public array  $managed_groups_ids,
        public string $name,
        public string $email,
        public array  $roles,
    )
    {
    }

    public static function fromEntity(User $entity): self
    {
        return new self(
            $entity->getId(),
            array_map(
                fn(Group $group) => $group->getId(),
                $entity->getGroupMemberships()->toArray()
            ),
            array_map(
                fn(Room $room) => $room->getId(),
                $entity->getRoomMemberships()->toArray()
            ),
            array_map(
                fn(Room $room) => $room->getId(),
                $entity->getManagedRooms()->toArray()
            ),
            array_map(
                fn(Group $group) => $group->getId(),
                $entity->getManagedGroups()->toArray()
            ),
            $entity->getName(),
            $entity->getEmail(),
            $entity->getRoles(),
        );
    }
}

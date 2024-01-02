<?php

namespace App\Api\DTO;

use App\Entity\Room;
use App\Entity\User;

class RoomResponse
{
    public function __construct(
        public int    $id,
        public array  $members_ids,
        public array  $managers_ids,
        public string $building,
        public string $name,
        public ?int   $group_id,
        public bool   $is_public,
        public bool   $is_locked,
    )
    {
    }

    public static function fromEntity(Room $entity): self
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
            $entity->getBuilding(),
            $entity->getName(),
            $entity->getGroup()?->getId(),
            $entity->isPublic(),
            $entity->isLocked(),
        );
    }
}
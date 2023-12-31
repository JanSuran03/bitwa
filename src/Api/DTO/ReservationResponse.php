<?php

namespace App\Api\DTO;

use App\Entity\Reservation;
use App\Entity\User;

class ReservationResponse
{
    public function __construct(
        public int                $id,
        public bool               $is_approved,
        public \DateTimeInterface $time_from,
        public \DateTimeInterface $time_to,
        public UserResponse       $author,
        public UserResponse       $responsible_user,
        public array              $invited_users,
        public RoomResponse       $room,
    )
    {
    }

    public static function fromEntity(Reservation $entity): self
    {
        return new self(
            $entity->getId(),
            $entity->isApproved(),
            $entity->getTimeFrom(),
            $entity->getTimeTo(),
            UserResponse::fromEntity($entity->getAuthor()),
            UserResponse::fromEntity($entity->getResponsibleUser()),
            array_map(
                fn(User $user) => UserResponse::fromEntity($user),
                $entity->getInvitedUsers()
            ),
            RoomResponse::fromEntity($entity->getRoom()),
        );
    }
}

<?php

namespace App\Api\DTO;

use App\Entity\Reservation;
use App\Entity\User;

class ReservationResponse
{
    public function __construct(
        public int                    $id,
        public bool                   $is_approved,
        public \DateTimeInterface     $time_from,
        public \DateTimeInterface     $time_to,
        public SimplifiedUserResponse $author,
        public SimplifiedUserResponse $responsible_user,
        public array                  $invited_users,
        public RoomResponse           $room,
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
            SimplifiedUserResponse::fromEntity($entity->getAuthor()),
            SimplifiedUserResponse::fromEntity($entity->getResponsibleUser()),
            array_map(
                fn(User $user) => SimplifiedUserResponse::fromEntity($user),
                $entity->getInvitedUsers()
            ),
            RoomResponse::fromEntity($entity->getRoom()),
        );
    }
}

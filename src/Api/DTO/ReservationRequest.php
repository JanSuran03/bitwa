<?php

namespace App\Api\DTO;

class ReservationRequest
{
    public function __construct(
        public ?bool               $is_approved,
        public ?\DateTimeInterface $time_from,
        public ?\DateTimeInterface $time_to,
        public ?int                $author_id,
        public ?int                $responsible_user_id,
        public array               $invited_users_ids,
        public ?int                $room_id,
    ) {}
}

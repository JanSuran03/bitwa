<?php

namespace App\Api\DTO;

use App\Validator as CustomAssert;
use Doctrine\Common\Collections\ArrayCollection;

#[CustomAssert\FutureTimespan]
#[CustomAssert\ChronologicalTimespan]
#[CustomAssert\RoomAvailability]
class ReservationRequest
{
    public ?bool $is_approved = null;

    public ?\DateTimeInterface $time_from = null;

    public ?\DateTimeInterface $time_to = null;

    public ?int $author_id = null;

    public ?int $responsible_user_id = null;

    public ArrayCollection $invited_users_ids;

    public ?int $room_id = null;

    public function __construct(
        ?bool               $is_approved,
        ?\DateTimeInterface $time_from,
        ?\DateTimeInterface $time_to,
        ?int                $author_id,
        ?int                $responsible_user_id,
        array               $invited_users_ids,
        ?int                $room_id,
    )
    {
        $this->is_approved = $is_approved;
        $this->time_from = $time_from;
        $this->time_to = $time_to;
        $this->author_id = $author_id;
        $this->responsible_user_id = $responsible_user_id;
        $this->invited_users_ids = new ArrayCollection($invited_users_ids);
        $this->room_id = $room_id;
    }
}

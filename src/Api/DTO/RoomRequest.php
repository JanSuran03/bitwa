<?php

namespace App\Api\DTO;

class RoomRequest
{
    public function __construct(
        public ?string $building,
        public ?string $name,
        public ?int    $group_id,
        public ?bool   $is_public,
    )
    {
    }
}
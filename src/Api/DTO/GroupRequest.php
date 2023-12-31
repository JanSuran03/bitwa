<?php

namespace App\Api\DTO;

class GroupRequest
{
    public function __construct(
        public string $name,
    )
    {
    }
}
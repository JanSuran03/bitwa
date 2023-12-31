<?php

namespace App\Api\DTO;

class UserRequest
{
    public function __construct(
        public ?string $name,
        public ?string $email,
        public ?string $password,
    )
    {
    }
}

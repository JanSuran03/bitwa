<?php

namespace App\Api\DTO;

class LockResponse
{
    public function __construct(
        public bool $success,
        public bool $locked_now,
    )
    {
    }
}
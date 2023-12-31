<?php

namespace App\Api\DTO;

use App\Entity\User;

class SimplifiedUserResponse
{
    public function __construct(
        public int    $id,
        public string $name,
        public string $email,
    )
    {
    }

    public static function fromEntity(User $entity): self
    {
        return new self(
            $entity->getId(),
            $entity->getName(),
            $entity->getEmail(),
        );
    }
}
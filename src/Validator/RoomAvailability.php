<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class RoomAvailability extends Constraint
{
    public string $message = 'V zadaném termínu je tato učebna již obsazená. Zkuste jiný termín.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
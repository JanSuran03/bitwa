<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class FutureTimespan extends Constraint
{
    public string $message = 'Čas začátku rezervace musí být v budoucnosti.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
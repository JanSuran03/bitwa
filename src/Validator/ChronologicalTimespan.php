<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ChronologicalTimespan extends Constraint
{
    public string $message = 'Čas konce rezervace musí nastat později než čas začátku.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
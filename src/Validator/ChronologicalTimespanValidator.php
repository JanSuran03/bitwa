<?php

namespace App\Validator;

use App\Entity\Reservation;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ChronologicalTimespanValidator extends ConstraintValidator
{
    public function validate($reservation, Constraint $constraint): void
    {
        if (!$reservation instanceof Reservation) {
            throw new UnexpectedValueException($reservation, Reservation::class);
        }

        if (!$constraint instanceof ChronologicalTimespan) {
            throw new UnexpectedTypeException($constraint, ChronologicalTimespan::class);
        }

        if ($reservation->getTimeFrom() > $reservation->getTimeTo()) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('timeTo')
                ->addViolation();
        }
    }
}
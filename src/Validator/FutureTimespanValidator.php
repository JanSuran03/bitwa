<?php

namespace App\Validator;

use App\Entity\Reservation;
use DateTime;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class FutureTimespanValidator extends ConstraintValidator
{

    public function validate($reservation, Constraint $constraint): void
    {
        if (!$reservation instanceof Reservation) {
            throw new UnexpectedValueException($reservation, Reservation::class);
        }

        if (!$constraint instanceof FutureTimespan) {
            throw new UnexpectedTypeException($constraint, FutureTimespan::class);
        }

        // If this reservation already exists in the database, don't re-validate
        if ($reservation->getId()) {
            return;
        }

        if ($reservation->getTimeFrom() < new DateTime('now')) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('timeFrom')
                ->addViolation();
        }
    }
}
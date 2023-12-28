<?php

namespace App\Validator;

use App\Entity\Reservation;
use App\Service\RoomService;
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

        if ($reservation->getTimeFrom() < new DateTime('now')) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('timeFrom')
                ->addViolation();
        }
    }
}
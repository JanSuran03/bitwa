<?php

namespace App\Validator;

use App\Entity\Reservation;
use App\Service\RoomService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class RoomAvailabilityValidator extends ConstraintValidator
{
    private RoomService $roomService;

    public function __construct(RoomService $roomService)
    {
        $this->roomService = $roomService;
    }

    public function validate($reservation, Constraint $constraint): void
    {
        if (!$reservation instanceof Reservation) {
            throw new UnexpectedValueException($reservation, Reservation::class);
        }

        if (!$constraint instanceof RoomAvailability) {
            throw new UnexpectedTypeException($constraint, RoomAvailability::class);
        }

        // If this reservation already exists in the database, don't re-validate
        if ($reservation->getId()) {
            return;
        }

        if ($this->roomService->isBookedBetween($reservation->getRoom(), $reservation->getTimeFrom(), $reservation->getTimeTo())) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('timeFrom')
                ->addViolation();
        }
    }
}
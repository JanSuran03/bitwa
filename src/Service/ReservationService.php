<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\Room;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use function Symfony\Component\Clock\now;

class ReservationService
{
    private EntityRepository $reservationRepository;
    private RoomService $roomService;

    public function __construct(EntityManagerInterface $entityManager, RoomService $roomService)
    {
        $this->reservationRepository = $entityManager->getRepository(Reservation::class);
        $this->roomService = $roomService;
    }

    public function getAll(): array
    {
        return $this->reservationRepository->findAll();
    }

    public function getAllByAuthor(User $user): array
    {
        return $this->reservationRepository->findBy(['author' => $user]);
    }


    public function getAllByInvitee(User $user): array
    {
        $allReservations = $this->reservationRepository->findAll();
        return array_values(
            array_filter(
                $allReservations,
                fn ($reservation) => in_array($user, $reservation->getInvitedUsers())
            )
        );
    }

    public function getAllByManager(User $user): array
    {
        $allReservations = $this->reservationRepository->findAll();

        /** @var Reservation[] $manageableReservations */
        $manageableReservations =
            array_values(
                array_filter(
                    $allReservations,
                    fn ($reservation) => $this->roomService->isTransitiveManagerOf($user, $reservation->getRoom())
                )
            );

        $toApprove = [];
        $approvedCurrent = [];
        $approvedComing = [];
        $approvedPast = [];
        foreach ($manageableReservations as $reservation) {
            if (!$reservation->isApproved()) {
                $toApprove[] = $reservation;
            } elseif ($this->isCurrent($reservation)) {
                $approvedCurrent[] = $reservation;
            } elseif ($this->isComing($reservation)) {
                $approvedComing[] = $reservation;
            } elseif ($this->isPast($reservation)) {
                $approvedPast[] = $reservation;
            }
        }

        return [
            'toApprove' => $toApprove,
            'approvedCurrent' => $approvedCurrent,
            'approvedComing' => $approvedComing,
            'approvedPast' => $approvedPast,
        ];
    }

    public function getOneById(int $id): Reservation
    {
        return $this->reservationRepository->find($id);
    }

    public function approveById(int $id): void
    {
        $reservation = $this->reservationRepository->find($id);
        $reservation->setApproved(true);
        $this->reservationRepository->flush();
    }

    public function create(Reservation $reservation)
    {
        $this->reservationRepository->create($reservation);
    }

    private function isCurrent(Reservation $reservation): bool
    {
        return $reservation->getTimeFrom() < now() && now() < $reservation->getTimeTo();
    }

    private function isComing(Reservation $reservation): bool
    {
        return now() < $reservation->getTimeFrom();
    }
    private function isPast(Reservation $reservation): bool
    {
        return $reservation->getTimeTo() < now();
    }
}

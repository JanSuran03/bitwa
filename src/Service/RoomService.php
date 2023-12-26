<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use function Symfony\Component\Clock\now;

class RoomService {
    private EntityRepository $roomRepository;
    private EntityRepository $reservationRepository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->roomRepository = $entityManager->getRepository(Room::class);
        $this->reservationRepository = $entityManager->getRepository(Reservation::class);
    }

    public function getAll(): array {
        return $this->roomRepository->findAll();
    }

    public function getAllByName(?string $query): array {
        $allRooms = $this->roomRepository->findAll();

        if ($query === null || $query === '')
            return $allRooms;

        return array_values(
            array_filter(
                $allRooms,
                fn ($room) => str_contains(strtolower($room->getFullName()), strtolower($query))
            )
        );
    }

    public function getOneById(int $id): Room {
        return $this->roomRepository->find($id);
    }

    public function unlockById(int $id) {
        // TODO
    }

    public function lockById(int $id) {
        // TODO
    }

    public function getCurrentAvailabilityMap(array $rooms): array {
        $map = [];
        foreach ($rooms as $room) {
            $roomId = $room->getId();
            if ($this->isBookedNow($room))
                $map[$roomId] = false;
            else
                $map[$roomId] = true;
        }
        return $map;
    }

    private function isBookedNow(Room $room): ?bool {
        $result = false;
        $reservations = $this->reservationRepository->findBy(['room' => $room]);
        foreach ($reservations as $reservation) {
            if ($reservation->getTimeFrom() < now() && $reservation->getTimeTo() > now() && $reservation->isIsApproved()) {
                $result = true;
                break;
            }
        }
        return $result;
    }
}

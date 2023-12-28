<?php

namespace App\Service;

use App\Entity\Room;
use App\Repository\ReservationRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\SecurityBundle\Security;
use function Symfony\Component\Clock\now;

class RoomService {
    private RoomRepository $roomRepository;
    private ReservationRepository $reservationRepository;
    private Security $security;

    public function __construct(RoomRepository        $roomRepository,
                                ReservationRepository $reservationRepository,
                                Security              $security) {
        $this->roomRepository = $roomRepository;
        $this->reservationRepository = $reservationRepository;
        $this->security = $security;
    }

    public function getAll(): array {
        if ($this->security->getUser() != null) {
            return $this->roomRepository->findAll();
        } else {
            return $this->roomRepository->findPublicRooms();
        }
    }

    public function getAllByName(?string $query): array {
        $allRooms = $this->getAll();

        if ($query === null || $query === '')
            return $allRooms;

        return array_values(
            array_filter(
                $allRooms,
                fn($room) => str_contains(strtolower($room->getFullName()), strtolower($query))
            )
        );
    }

    public function findOneByName(string $name) : ?Room {
        error_log($name);
        return $this->roomRepository->findByName($name);
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

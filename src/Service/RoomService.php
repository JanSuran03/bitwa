<?php

namespace App\Service;

use App\Entity\Room;
use App\Entity\User;
use App\Repository\ReservationRepository;
use App\Repository\RoomRepository;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
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

    public function createRoom(string $name, string $building, bool $isPublic): Room {
        return $this->roomRepository->createRoom($name, $building, $isPublic);
    }

    public function setRoom(Room $room): void {
        $this->roomRepository->setRoom($room);
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

    public function findByNameAndBuilding(string $name, string $building): ?Room {
        error_log($name);
        return $this->roomRepository->findByNameAndBuilding($name, $building);
    }

    public function getOneById(int $id): ?Room {
        return $this->roomRepository->find($id);
    }

    public function unlockById(int $id) {
        // TODO
    }

    public function lockById(int $id) {
        // TODO
    }

    public function isBookedBetween(Room $room, DateTimeInterface $from, DateTimeInterface $to, bool $onlyApproved = false): bool {
        $conditions = ['room' => $room];
        if ($onlyApproved) {
            $conditions['is_approved'] = true;
        }
        $reservations = $this->reservationRepository->findBy($conditions);

        foreach ($reservations as $reservation) {
            if ($reservation->getTimeFrom() < $to && $reservation->getTimeTo() > $from) {
                return true;
            }
        }
        return false;
    }

    public function isOccupiedNow(Room $room): bool {
        return $this->isBookedBetween($room, now(), now(), true);
    }

    public function getCurrentAvailabilityMap(array $rooms): array {
        $map = [];
        foreach ($rooms as $room) {
            $roomId = $room->getId();
            if ($this->isOccupiedNow($room))
                $map[$roomId] = false;
            else
                $map[$roomId] = true;
        }
        return $map;
    }

    public function isTransitiveMemberOf(User $user, Room $room): bool {
        if ($this->isTransitiveManagerOf($user, $room)) {
            return true;
        }
        if ($room->getMembers()->contains($user)) {
            return true;
        }
        $group = $room->getGroup();
        while ($group !== null) {
            if ($group->getMembers()->contains($user)) {
                return true;
            }
            $group = $group->getParent();
        }
        return false;
    }

    public function isTransitiveManagerOf(User $user, Room $room): bool {
        if ($room->getManagers()->contains($user)) {
            return true;
        }
        $group = $room->getGroup();
        while ($group !== null) {
            if ($group->getManagers()->contains($user)) {
                return true;
            }
            $group = $group->getParent();
        }
        return false;
    }

    public function isBookableBy(Room $room, User $user): bool {
        return $room->isPublic() || $this->isTransitiveMemberOf($user, $room);
    }

    public function getAllBookableBy(User $user): array {
        $allRooms = $this->getAll();
        return array_values(
            array_filter(
                $allRooms,
                fn($room) => $this->isBookableBy($room, $user)
            )
        );
    }

    public function getAllManageableBy(User $user): array {
        $allRooms = $this->getAll();
        return array_values(
            array_filter(
                $allRooms,
                fn($room) => $this->isTransitiveManagerOf($user, $room)
            )
        );
    }
}

<?php

namespace App\Service;

use App\Api\DTO\LockResponse;
use App\Entity\Reservation;
use App\Entity\Room;
use App\Entity\User;
use App\Repository\ReservationRepository;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use DateTimeInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function Symfony\Component\Clock\now;

class RoomService
{
    private RoomRepository $roomRepository;
    private ReservationRepository $reservationRepository;
    private UserRepository $userRepository;
    private Security $security;

    public function __construct(RoomRepository        $roomRepository,
                                ReservationRepository $reservationRepository,
                                UserRepository        $userRepository,
                                Security              $security)
    {
        $this->roomRepository = $roomRepository;
        $this->reservationRepository = $reservationRepository;
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    public function isBookedBetween(Room $room, DateTimeInterface $from, DateTimeInterface $to, bool $onlyApproved = false): bool
    {
        $conditions = ['room' => $room];
        if ($onlyApproved) {
            $conditions['isApproved'] = true;
        }
        $reservations = $this->reservationRepository->findBy($conditions);

        foreach ($reservations as $reservation) {
            if ($reservation->getTimeFrom() < $to && $reservation->getTimeTo() > $from) {
                return true;
            }
        }
        return false;
    }

    public function isOccupiedNow(Room $room): bool
    {
        return $this->isBookedBetween($room, now(), now(), true);
    }

    public function isAllowed(int $roomId, int $userId): bool
    {
        $room = $this->roomRepository->find($roomId);
        $user = $this->userRepository->find($userId);

        // If unlocked, free access for everyone
        if (!$room->isLocked()) {
            return true;
        }

        // If currently booked, check invitees list
        $currentReservation = $this->getCurrentReservationByRoom($room, true);
        if ($currentReservation) {
            return in_array($user, $currentReservation->getInvitedUsers());
        }

        // If currently not booked and public, free access for everyone
        if ($room->isPublic()) {
            return true;
        }

        // If currently not booked and private, check member (and manager) list
        return $this->isTransitiveMemberOf($user, $room);
    }

    public function isTransitiveMemberOf(User $user, Room $room): bool
    {
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

    public function isTransitiveManagerOf(User $user, Room $room): bool
    {
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

    public function isBookableBy(Room $room, User $user): bool
    {
        return $room->isPublic() || $this->isTransitiveMemberOf($user, $room);
    }

    public function getCurrentReservationByRoom(Room $room, bool $onlyApproved = false): ?Reservation
    {
        $conditions = ['room' => $room];
        if ($onlyApproved) {
            $conditions['isApproved'] = true;
        }
        $reservations = $this->reservationRepository->findBy($conditions);

        foreach ($reservations as $reservation) {
            if ($reservation->getTimeFrom() < now() && $reservation->getTimeTo() > now()) {
                return $reservation;
            }
        }
        return null;
    }

    public function getCurrentAvailabilityMap(array $rooms): array
    {
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

    public function create(Room $room): Room
    {
        return $this->roomRepository->createOrUpdate($room);
    }

    public function getAllByApiQueries(array $queries): array
    {
        if ($this->security->getUser() == null) {
            $queries['public'] = 'true';
        }
        return $this->roomRepository->findAllByApiQueries($queries);
    }

    public function getAll(): array
    {
        return $this->getAllByApiQueries([]);
    }

    public function getAllByFullNameSubstring(?string $query): array
    {
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

    public function getAllBookableBy(User $user): array
    {
        $allRooms = $this->getAll();
        return array_values(
            array_filter(
                $allRooms,
                fn($room) => $this->isBookableBy($room, $user)
            )
        );
    }

    public function getAllManageableBy(User $user): array
    {
        $allRooms = $this->getAll();
        return array_values(
            array_filter(
                $allRooms,
                fn($room) => $this->isTransitiveManagerOf($user, $room)
            )
        );
    }

    public function getOneById(int $id): ?Room
    {
        $room = $this->roomRepository->find($id);
        if (!$room) {
            throw new NotFoundHttpException('Room with ID ' . $id . ' not found');
        }
        return $room;
    }

    public function getOneByNameAndBuilding(string $name, string $building): ?Room
    {
        return $this->roomRepository->findOneBy([
            "name" => $name,
            "building" => $building
        ]);
    }

    public function update(Room $room): Room
    {
        return $this->roomRepository->createOrUpdate($room);
    }

    public function addMember(Room $room, User $member): Room
    {
        $room->addMember($member);
        return $this->roomRepository->createOrUpdate($room);
    }

    public function removeMember(Room $room, User $member): Room
    {
        if (!in_array($member, $room->getMembers()->toArray())) {
            throw new NotFoundHttpException('User with ID ' . $member->getId() . ' not found in the list of members');
        }
        $room->removeMember($member);
        return $this->roomRepository->createOrUpdate($room);
    }

    public function addManager(Room $room, User $manager): Room
    {
        $room->addManager($manager);
        return $this->roomRepository->createOrUpdate($room);
    }

    public function removeManager(Room $room, User $manager): Room
    {
        if (!in_array($manager, $room->getManagers()->toArray())) {
            throw new NotFoundHttpException('User with ID ' . $manager->getId() . ' not found in the list of managers');
        }
        $room->removeManager($manager);
        return $this->roomRepository->createOrUpdate($room);
    }

    private function switchLock(Room $room): LockResponse
    {
        $room->setLocked(!$room->isLocked());
        $this->roomRepository->createOrUpdate($room);
        return new LockResponse(true, $room->isLocked());
    }

    public function doubleTapById(int $roomId, int $userId): LockResponse
    {
        $room = $this->roomRepository->find($roomId);
        $user = $this->userRepository->find($userId);

        $currentReservation = $this->getCurrentReservationByRoom($room, true);
        if ($currentReservation) {

            // If currently booked, only the responsible user can lock/unlock for free access
            if ($user === $currentReservation->getResponsibleUser()) {
                return $this->switchLock($room);
            } else {
                return new LockResponse(false, $room->isLocked());
            }

        } else {

            // If currently not booked, all members (and managers) can lock/unlock for free access
            if ($this->isTransitiveMemberOf($user, $room)) {
                return $this->switchLock($room);
            } else {
                return new LockResponse(false, $room->isLocked());
            }
        }
    }

    public function deleteById(int $id): void
    {
        $room = $this->roomRepository->find($id);
        if (!$room) {
            throw new NotFoundHttpException('Room with ID ' . $id . ' not found');
        }
        $this->roomRepository->delete($room);
    }
}

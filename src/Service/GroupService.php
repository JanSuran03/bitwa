<?php

namespace App\Service;

use App\Entity\Group;
use App\Entity\Room;
use App\Entity\User;
use App\Repository\GroupRepository;
use App\Repository\ReservationRepository;
use App\Repository\RoomRepository;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\SecurityBundle\Security;
use function Symfony\Component\Clock\now;

class GroupService {
    private GroupRepository $groupRepository;

    public function __construct(GroupRepository $groupRepository) {
        $this->groupRepository = $groupRepository;
    }

    public function findAll(): array {
        return $this->groupRepository->findAll();
    }

    public function findById(int $id): ?Group {
        return $this->groupRepository->find($id);
    }

    public function findByName(string $name): ?Group {
        return $this->groupRepository->findOneBy(['name' => $name]);
    }

    public function addGroup(Group $group): void {
        $this->groupRepository->addGroup($group);
    }
}

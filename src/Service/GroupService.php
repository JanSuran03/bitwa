<?php

namespace App\Service;

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

    public function __construct(GroupRepository $groupRepository,) {
        $this->groupRepository = $groupRepository;
    }
}

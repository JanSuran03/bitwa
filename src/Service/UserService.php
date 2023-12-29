<?php

namespace App\Service;

use App\Entity\Room;
use App\Entity\User;
use App\Repository\ReservationRepository;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\SecurityBundle\Security;
use function Symfony\Component\Clock\now;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAll(): array
    {
        return $this->userRepository->findAll();
    }

    public function getOneById(int $id): User
    {
        return $this->userRepository->find($id);
    }

    public function getInviteChoices(User $user): array {
        $allUsers = $this->getAll();
        $inviteChoices = [];
        foreach ($allUsers as $potentialInviteChoice) {
            if ($potentialInviteChoice !== $user) {
                $inviteChoices[$potentialInviteChoice->getName()] = $potentialInviteChoice;
            }
        }
        return $inviteChoices;
    }
}

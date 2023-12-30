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

    public function getUserChoices(): array
    {
        $allUsers = $this->getAll();
        $responsibleChoices = [];
        foreach ($allUsers as $responsibleChoice) {
            $responsibleChoices[$responsibleChoice->getName()] = $responsibleChoice;
        }
        return $responsibleChoices;
    }
}

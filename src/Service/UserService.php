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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function Symfony\Component\Clock\now;

class UserService {
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function getAll(): array {
        return $this->userRepository->findAll();
    }

    public function getOneById(int $id): User {
        $user = $this->userRepository->find($id);
        if (!$user) {
            throw new NotFoundHttpException('User with ID ' . $id . ' not found');
        }
        return $user;
    }

    public function findOneByEmail(string $email): ?User {
        return $this->userRepository->findByEmail($email);
    }

    public function getUserChoices(): array {
        $allUsers = $this->getAll();
        $responsibleChoices = [];
        foreach ($allUsers as $responsibleChoice) {
            $responsibleChoices[$responsibleChoice->getName()] = $responsibleChoice;
        }
        return $responsibleChoices;
    }

    public function setUserName(User $user, string $newName): void {
        $user->setName($newName);
        $this->userRepository->setUser($user);
    }

    public function setEmail(User $user, string $newEmail): void {
        $user->setEmail($newEmail);
        $this->userRepository->setUser($user);
    }

    public function setUser(User $user) : void {
        $this->userRepository->setUser($user);
    }

    public function deleteById(int $id): void
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            throw new NotFoundHttpException('User with ID ' . $id . ' not found');
        }
        $this->userRepository->delete($user);
    }
}

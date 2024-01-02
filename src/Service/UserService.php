<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createUser(User $user): User
    {
        return $this->userRepository->createOrUpdate($user);
    }

    public function getAll(): array
    {
        return $this->userRepository->findAll();
    }

    public function getAllByApiQueries(array $queries): array
    {
        return $this->userRepository->findByApiQueries($queries);
    }

    public function getOneById(int $id): User
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            throw new NotFoundHttpException('User with ID ' . $id . ' not found');
        }
        return $user;
    }

    public function findOneByEmail(string $email): ?User
    {
        return $this->userRepository->findOneBy([
            "email" => $email
        ]);
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

    public function setUserName(User $user, string $newName): void
    {
        $user->setName($newName);
        $this->userRepository->createOrUpdate($user);
    }

    public function setEmail(User $user, string $newEmail): void
    {
        $user->setEmail($newEmail);
        $this->userRepository->createOrUpdate($user);
    }

    public function updateUser(User $user): void
    {
        $this->userRepository->createOrUpdate($user);
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

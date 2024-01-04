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

    public function create(User $user): User
    {
        return $this->userRepository->createOrUpdate($user);
    }

    public function getAllByApiQueries(array $queries): array
    {
        return $this->userRepository->findByApiQueries($queries);
    }

    public function getAll(): array
    {
        return $this->userRepository->findByApiQueries([]);
    }

    public function getOneById(int $id): User
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            throw new NotFoundHttpException('User with ID ' . $id . ' not found');
        }
        return $user;
    }

    public function getOneByEmail(string $email): ?User
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

    public function getPossibleManagers(array $alreadyManagers): array
    {
        $allUsers = $this->getAll();
        $possibleManagers = [];
        foreach ($allUsers as $possibleManager) {
            if (!in_array($possibleManager, $alreadyManagers)) {
                $possibleManagers[$possibleManager->getName()] = $possibleManager;
            }
        }
        return $possibleManagers;
    }

    public function update(User $user): void
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

    public function addMember($user, $group): User
    {
        $user->addGroupMembership($group);
        return $this->userRepository->createOrUpdate($user);
    }

    public function addManager($user, $group): User
    {
        $user->addManagedGroup($group);
        return $this->userRepository->createOrUpdate($user);
    }

    public function removeMember($user, $group): User
    {
        $user->removeGroupMembership($group);
        return $this->userRepository->createOrUpdate($user);
    }

    public function removeManager($user, $group): User
    {
        $user->removeManagedGroup($group);
        return $this->userRepository->createOrUpdate($user);
    }
}

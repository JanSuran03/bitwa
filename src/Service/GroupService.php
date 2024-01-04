<?php

namespace App\Service;

use App\Entity\Group;
use App\Entity\Room;
use App\Entity\User;
use App\Repository\GroupRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GroupService
{
    private GroupRepository $groupRepository;

    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function isTransitiveManagerOf(User $user, Group $group): bool
    {
        while ($group !== null) {
            if ($group->getManagers()->contains($user)) {
                return true;
            }
            $group = $group->getParent();
        }
        return false;
    }

    public function findAll(): array
    {
        return $this->groupRepository->findAll();
    }

    public function getAllByApiQueries(array $queries): array
    {
        return $this->groupRepository->findAllByApiQueries($queries);
    }

    public function findById(int $id): ?Group
    {
        return $this->groupRepository->find($id);
    }

    public function findByName(string $name): ?Group
    {
        return $this->groupRepository->findOneBy(['name' => $name]);
    }

    public function create(Group $group): void
    {
        $this->groupRepository->createOrUpdate($group);
    }

    public function addMember(Group $group, User $member): Group
    {
        $group->addMember($member);
        return $this->groupRepository->createOrUpdate($group);
    }

    public function removeMember(Group $group, User $member): Group
    {
        if (!in_array($member, $group->getMembers()->toArray())) {
            throw new NotFoundHttpException('User with ID ' . $member->getId() . ' not found in the list of members');
        }
        $group->removeMember($member);
        return $this->groupRepository->createOrUpdate($group);
    }

    public function addManager(Group $group, User $manager): Group
    {
        $group->addManager($manager);
        return $this->groupRepository->createOrUpdate($group);
    }

    public function removeManager(Group $group, User $manager): Group
    {
        if (!in_array($manager, $group->getManagers()->toArray())) {
            throw new NotFoundHttpException('User with ID ' . $manager->getId() . ' not found in the list of managers');
        }
        $group->removeManager($manager);
        return $this->groupRepository->createOrUpdate($group);
    }

    public function addRoom(Group $group, Room $room): Group
    {
        $group->addRoom($room);
        return $this->groupRepository->createOrUpdate($group);
    }

    public function removeRoom(Group $group, Room $room): Group
    {
        if (!in_array($room, $group->getRooms()->toArray())) {
            throw new NotFoundHttpException('Room with ID ' . $room->getId() . ' not found in the list of this group\'s rooms');
        }
        $group->removeRoom($room);
        return $this->groupRepository->createOrUpdate($group);
    }

    public function deleteById(int $id): void
    {
        $group = $this->groupRepository->find($id);
        if (!$group) {
            throw new NotFoundHttpException('Group with ID ' . $id . ' not found');
        }
        $this->groupRepository->delete($group);
    }
}

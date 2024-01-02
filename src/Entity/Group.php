<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: 'groups')]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'groupMemberships')]
    private Collection $members;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'managedGroups')]
    private Collection $managers;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'childGroups')]
    private ?self $parent;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    private Collection $childGroups;

    #[ORM\OneToMany(mappedBy: 'group', targetEntity: Room::class)]
    private Collection $rooms;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->managers = new ArrayCollection();
        $this->parent = null;
        $this->childGroups = new ArrayCollection();
        $this->rooms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, User>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): static
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
            $member->addGroupMembership($this);
        }

        return $this;
    }

    public function removeMember(User $member): static
    {
        if ($this->members->removeElement($member)) {
            $member->removeGroupMembership($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getManagers(): Collection
    {
        return $this->managers;
    }

    public function addManager(User $manager): static
    {
        if (!$this->managers->contains($manager)) {
            $this->managers->add($manager);
            $manager->addManagedGroup($this);
        }

        return $this;
    }

    public function removeManager(User $manager): static
    {
        if ($this->managers->removeElement($manager)) {
            $manager->removeManagedGroup($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getChildGroups(): Collection
    {
        return $this->childGroups;
    }

    public function addChildGroup(Group $group): static
    {
        if (!$this->childGroups->contains($group)) {
            $this->childGroups->add($group);
            $group->setParent($this);
        }

        return $this;
    }

    public function removeChildGroup(Group $group): static
    {
        if ($this->childGroups->removeElement($group)) {
            $group->setParent(null);
        }

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?Group $parent): void
    {
        // To avoid infinite recursion, because addChildGroup()
        // and removeChildGroup() also call this method
        if ($this->parent && $this->parent === $parent) {
            return;
        }

        $this->parent?->removeChildGroup($this);
        $this->parent = $parent;
        $this->parent?->addChildGroup($this);
    }

    /**
     * @return Collection<int, Room>
     */
    public function getRooms(): Collection
    {
        return $this->rooms;
    }

    public function addRoom(Room $room): static
    {
        if (!$this->rooms->contains($room)) {
            $this->rooms->add($room);
            $room->setGroup($this);
        }

        return $this;
    }

    public function removeRoom(Room $room): static
    {
        if ($this->rooms->removeElement($room)) {
            // set the owning side to null (unless already changed)
            if ($room->getGroup() === $this) {
                $room->setGroup(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}

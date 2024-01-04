<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
#[ORM\Table(name: 'rooms')]
#[ORM\UniqueConstraint(
    name: 'room_name_unique_constraint',
    columns: ['building', 'name']
)]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'roomMemberships')]
    private Collection $members;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'managedRooms')]
    private Collection $managers;

    #[ORM\Column]
    private ?string $building;

    #[ORM\Column]
    private ?string $name;

    #[ORM\ManyToOne(inversedBy: 'rooms')]
    private ?Group $group = null;

    #[ORM\Column]
    private ?bool $isPublic;

    #[ORM\Column]
    private ?bool $isLocked;

    public function __construct(?string $building = null, ?string $name = null, ?bool $isPublic = null)
    {
        $this->members = new ArrayCollection();
        $this->managers = new ArrayCollection();
        $this->building = $building;
        $this->name = $name;
        $this->isPublic = $isPublic;
        $this->isLocked = true;
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
            $member->addRoomMembership($this);
        }

        return $this;
    }

    public function removeMember(User $member): static
    {
        if ($this->members->removeElement($member)) {
            $member->removeRoomMembership($this);
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
            $manager->addManagedRoom($this);
        }

        return $this;
    }

    public function removeManager(User $manager): static
    {
        if ($this->managers->removeElement($manager)) {
            $manager->removeManagedRoom($this);
        }

        return $this;
    }

    public function getBuilding(): ?string
    {
        return $this->building;
    }

    public function setBuilding(string $building): static
    {
        $this->building = $building;

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

    public function getFullName(): ?string
    {
        return $this->building . ':' . $this->name;
    }

    public function getUrlName(): ?string
    {
        return $this->building . '-' . $this->name;
    }

    public function isPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setPublic(bool $isPublic): static
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    public function isLocked(): ?bool
    {
        return $this->isLocked;
    }

    public function setLocked(bool $isLocked): static
    {
        $this->isLocked = $isLocked;

        return $this;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(?Group $group): void
    {
        $this->group = $group;
    }
}

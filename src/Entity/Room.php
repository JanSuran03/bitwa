<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
#[ORM\Table(name: 'rooms')]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'room_memberships')]
    private Collection $members;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'managed_rooms')]
    private Collection $managers;

    #[ORM\Column]
    private ?string $building = null;

    #[ORM\Column]
    private ?string $number = null;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->managers = new ArrayCollection();
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

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->building . ':' . $this->number;
    }

    public function getUrlName(): ?string
    {
        return $this->building . '-' . $this->number;
    }
}

<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Reservation::class)]
    private Collection $reservations;

    #[ORM\ManyToMany(targetEntity: Group::class, inversedBy: 'members')]
    #[ORM\JoinTable(name: 'group_members')]
    private Collection $group_memberships;

    #[ORM\ManyToMany(targetEntity: Room::class, inversedBy: 'members')]
    #[ORM\JoinTable(name: 'room_members')]
    private Collection $room_memberships;

    #[ORM\ManyToMany(targetEntity: Room::class, inversedBy: 'managers')]
    #[ORM\JoinTable(name: 'room_managers')]
    private Collection $managed_rooms;

    #[ORM\ManyToMany(targetEntity: Group::class, inversedBy: 'managers')]
    #[ORM\JoinTable(name: 'group_managers')]
    private Collection $managed_groups;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->group_memberships = new ArrayCollection();
        $this->room_memberships = new ArrayCollection();
        $this->managed_rooms = new ArrayCollection();
        $this->managed_groups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setAuthor($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getAuthor() === $this) {
                $reservation->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getGroupMemberships(): Collection
    {
        return $this->group_memberships;
    }

    public function addGroupMembership(Group $groupMembership): static
    {
        if (!$this->group_memberships->contains($groupMembership)) {
            $this->group_memberships->add($groupMembership);
        }

        return $this;
    }

    public function removeGroupMembership(Group $groupMembership): static
    {
        $this->group_memberships->removeElement($groupMembership);

        return $this;
    }

    /**
     * @return Collection<int, Room>
     */
    public function getRoomMemberships(): Collection
    {
        return $this->room_memberships;
    }

    public function addRoomMembership(Room $roomMembership): static
    {
        if (!$this->room_memberships->contains($roomMembership)) {
            $this->room_memberships->add($roomMembership);
        }

        return $this;
    }

    public function removeRoomMembership(Room $roomMembership): static
    {
        $this->room_memberships->removeElement($roomMembership);

        return $this;
    }

    /**
     * @return Collection<int, Room>
     */
    public function getManagedRooms(): Collection
    {
        return $this->managed_rooms;
    }

    public function addManagedRoom(Room $managedRoom): static
    {
        if (!$this->managed_rooms->contains($managedRoom)) {
            $this->managed_rooms->add($managedRoom);
        }

        return $this;
    }

    public function removeManagedRoom(Room $managedRoom): static
    {
        $this->managed_rooms->removeElement($managedRoom);

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getManagedGroups(): Collection
    {
        return $this->managed_groups;
    }

    public function addManagedGroup(Group $managedGroup): static
    {
        if (!$this->managed_groups->contains($managedGroup)) {
            $this->managed_groups->add($managedGroup);
        }

        return $this;
    }

    public function removeManagedGroup(Group $managedGroup): static
    {
        $this->managed_groups->removeElement($managedGroup);

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }
}

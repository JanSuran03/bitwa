<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Reservation::class)]
    private Collection $reservations;

    #[ORM\ManyToMany(targetEntity: Group::class, inversedBy: 'members')]
    #[ORM\JoinTable(name: 'group_members')]
    private Collection $groupMemberships;

    #[ORM\ManyToMany(targetEntity: Room::class, inversedBy: 'members')]
    #[ORM\JoinTable(name: 'room_members')]
    private Collection $roomMemberships;

    #[ORM\ManyToMany(targetEntity: Room::class, inversedBy: 'managers')]
    #[ORM\JoinTable(name: 'room_managers')]
    private Collection $managedRooms;

    #[ORM\ManyToMany(targetEntity: Group::class, inversedBy: 'managers')]
    #[ORM\JoinTable(name: 'group_managers')]
    private Collection $managedGroups;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->groupMemberships = new ArrayCollection();
        $this->roomMemberships = new ArrayCollection();
        $this->managedRooms = new ArrayCollection();
        $this->managedGroups = new ArrayCollection();
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
        return $this->groupMemberships;
    }

    public function addGroupMembership(Group $groupMembership): static
    {
        if (!$this->groupMemberships->contains($groupMembership)) {
            $this->groupMemberships->add($groupMembership);
        }

        return $this;
    }

    public function removeGroupMembership(Group $groupMembership): static
    {
        $this->groupMemberships->removeElement($groupMembership);

        return $this;
    }

    /**
     * @return Collection<int, Room>
     */
    public function getRoomMemberships(): Collection
    {
        return $this->roomMemberships;
    }

    public function addRoomMembership(Room $roomMembership): static
    {
        if (!$this->roomMemberships->contains($roomMembership)) {
            $this->roomMemberships->add($roomMembership);
        }

        return $this;
    }

    public function removeRoomMembership(Room $roomMembership): static
    {
        $this->roomMemberships->removeElement($roomMembership);

        return $this;
    }

    /**
     * @return Collection<int, Room>
     */
    public function getManagedRooms(): Collection
    {
        return $this->managedRooms;
    }

    public function addManagedRoom(Room $managedRoom): static
    {
        if (!$this->managedRooms->contains($managedRoom)) {
            $this->managedRooms->add($managedRoom);
        }

        return $this;
    }

    public function removeManagedRoom(Room $managedRoom): static
    {
        $this->managedRooms->removeElement($managedRoom);

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getManagedGroups(): Collection
    {
        return $this->managedGroups;
    }

    public function addManagedGroup(Group $managedGroup): static
    {
        if (!$this->managedGroups->contains($managedGroup)) {
            $this->managedGroups->add($managedGroup);
        }

        return $this;
    }

    public function removeManagedGroup(Group $managedGroup): static
    {
        $this->managedGroups->removeElement($managedGroup);

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

    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials()
    {
    }
}

<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as CustomAssert;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\Table(name: 'reservations')]
#[CustomAssert\FutureTimespan]
#[CustomAssert\ChronologicalTimespan]
#[CustomAssert\RoomAvailability]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?bool $is_approved = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank]
    #[Assert\Type(\DateTimeInterface::class)]
    private ?\DateTimeInterface $time_from = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank]
    #[Assert\Type(\DateTimeInterface::class)]
    private ?\DateTimeInterface $time_to = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[Assert\NotBlank]
    private ?User $author = null;

    #[ORM\ManyToMany(targetEntity: User::class)]
    private Collection $invited_users;

    #[ORM\ManyToOne]
    #[Assert\NotBlank]
    private ?Room $room = null;

    public function __construct()
    {
        $this->invited_users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isIsApproved(): ?bool
    {
        return $this->is_approved;
    }

    public function setIsApproved(bool $is_approved): static
    {
        $this->is_approved = $is_approved;

        return $this;
    }

    public function getTimeFrom(): ?\DateTimeInterface
    {
        return $this->time_from;
    }

    public function setTimeFrom(\DateTimeInterface $time_from): static
    {
        $this->time_from = $time_from;

        return $this;
    }

    public function getTimeTo(): ?\DateTimeInterface
    {
        return $this->time_to;
    }

    public function setTimeTo(\DateTimeInterface $time_to): static
    {
        $this->time_to = $time_to;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getInvitedUsers(): Collection
    {
        return $this->invited_users;
    }

    public function addInvitedUser(User $invitedUser): static
    {
        if (!$this->invited_users->contains($invitedUser)) {
            $this->invited_users->add($invitedUser);
        }

        return $this;
    }

    public function removeInvitedUser(User $invitedUser): static
    {
        $this->invited_users->removeElement($invitedUser);

        return $this;
    }

    public function getRoom(): Room
    {
        return $this->room;
    }

    public function setRoom(Room $room): static
    {
        $this->room = $room;

        return $this;
    }
}

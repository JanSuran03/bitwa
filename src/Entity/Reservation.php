<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use DateTime;
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

    #[ORM\ManyToOne]
    #[Assert\NotBlank]
    private ?User $responsible_user = null;

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'invitations')]
    private Collection $invited_users;

    #[ORM\ManyToOne]
    #[Assert\NotBlank]
    private ?Room $room = null;

    public function __construct(?Room $room = null, ?User $user = null, bool $autoApprove = false)
    {
        $this->is_approved = $autoApprove;
        $this->room = $room;
        $this->author = $user;
        $this->responsible_user = $user;
        date_default_timezone_set('Europe/Prague');
        $this->time_from = new DateTime('now');
        $this->time_to = new DateTime('now');
        $this->invited_users = new ArrayCollection([$user]);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isApproved(): ?bool
    {
        return $this->is_approved;
    }

    public function setApproved(bool $is_approved): static
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

    public function getResponsibleUser(): ?User
    {
        return $this->responsible_user;
    }

    public function setResponsibleUser(?User $responsible_user): void
    {
        $this->responsible_user = $responsible_user;
        $this->addInvitedUser($responsible_user);
    }

//    /**
//     * @return Collection<int, User>
//     */
    public function getInvitedUsers(): array
    {
        return $this->invited_users->toArray();
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
        if ($invitedUser !== $this->responsible_user)
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

<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use App\Validator as CustomAssert;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
    private ?bool $isApproved;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank]
    #[Assert\Type(\DateTimeInterface::class)]
    private ?\DateTimeInterface $timeFrom;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank]
    #[Assert\Type(\DateTimeInterface::class)]
    private ?\DateTimeInterface $timeTo;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[Assert\NotBlank]
    private ?User $author;

    #[ORM\ManyToOne]
    #[Assert\NotBlank]
    private ?User $responsibleUser;

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'invitations')]
    private Collection $invitedUsers;

    #[ORM\ManyToOne]
    #[Assert\NotBlank]
    private ?Room $room;

    public function __construct(?Room $room = null, ?User $user = null, bool $autoApprove = false)
    {
        $this->isApproved = $autoApprove;
        $this->room = $room;
        $this->author = $user;
        $this->responsibleUser = $user;
        date_default_timezone_set('Europe/Prague');
        $this->timeFrom = new DateTime('now');
        $this->timeTo = new DateTime('now');
        $this->invitedUsers = ($user) ? new ArrayCollection([$user]) : new ArrayCollection(([]));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isApproved(): ?bool
    {
        return $this->isApproved;
    }

    public function setApproved(bool $isApproved): static
    {
        $this->isApproved = $isApproved;

        return $this;
    }

    public function getTimeFrom(): ?\DateTimeInterface
    {
        return $this->timeFrom;
    }

    public function setTimeFrom(\DateTimeInterface $timeFrom): static
    {
        $this->timeFrom = $timeFrom;

        return $this;
    }

    public function getTimeTo(): ?\DateTimeInterface
    {
        return $this->timeTo;
    }

    public function setTimeTo(\DateTimeInterface $timeTo): static
    {
        $this->timeTo = $timeTo;

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
        return $this->responsibleUser;
    }

    public function setResponsibleUser(?User $responsibleUser): void
    {
        $this->responsibleUser = $responsibleUser;
        $this->addInvitedUser($responsibleUser);
    }

    public function getInvitedUsers(): array
    {
        return $this->invitedUsers->toArray();
    }

    public function addInvitedUser(User $invitedUser): static
    {
        if (!$this->invitedUsers->contains($invitedUser)) {
            $this->invitedUsers->add($invitedUser);
        }

        return $this;
    }

    public function removeInvitedUser(User $invitedUser): static
    {
        if ($invitedUser !== $this->responsibleUser)
            $this->invitedUsers->removeElement($invitedUser);

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

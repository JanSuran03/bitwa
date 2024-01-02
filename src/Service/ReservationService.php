<?php

namespace App\Service;

use App\Api\DTO\ReservationRequest;
use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TypeError;
use function Symfony\Component\Clock\now;

class ReservationService
{
    private EntityRepository $reservationRepository;
    private ValidatorInterface $validator;
    private RoomService $roomService;
    private UserService $userService;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator, RoomService $roomService, UserService $userService)
    {
        $this->reservationRepository = $entityManager->getRepository(Reservation::class);
        $this->validator = $validator;
        $this->roomService = $roomService;
        $this->userService = $userService;
    }

    private function isCurrent(Reservation $reservation): bool
    {
        return $reservation->getTimeFrom() < now() && now() < $reservation->getTimeTo();
    }

    private function isComing(Reservation $reservation): bool
    {
        return now() < $reservation->getTimeFrom();
    }

    private function isPast(Reservation $reservation): bool
    {
        return $reservation->getTimeTo() < now();
    }

    public function create(Reservation $reservation): Reservation
    {
        return $this->reservationRepository->createOrUpdate($reservation);
    }

    public function createFromRequestDto(ReservationRequest $reservationRequest): Reservation
    {
        if (!$reservationRequest->author_id) {
            throw new BadRequestHttpException('Missing mandatory field author_id');
        }
        try {
            $author = $this->userService->getOneById($reservationRequest->author_id);
        } catch (TypeError) {
            throw new NotFoundHttpException('Invalid author_id, user with ID ' . $reservationRequest->author_id . ' not found');
        }

        if (!$reservationRequest->responsible_user_id) {
            throw new BadRequestHttpException('Missing mandatory field responsible_user_id');
        }
        try {
            $responsibleUser = $this->userService->getOneById($reservationRequest->responsible_user_id);
        } catch (TypeError) {
            throw new NotFoundHttpException('Invalid responsible_user_id, user with ID ' . $reservationRequest->responsible_user_id . ' not found');
        }


        $invitedUsers = array_map(
            function (int $invitedUserId) {
                try {
                    $this->userService->getOneById($invitedUserId);
                } catch (TypeError) {
                    throw new NotFoundHttpException('Invalid invited_users_ids, user with ID ' . $invitedUserId . ' not found');
                }
            },
            $reservationRequest->invited_users_ids->toArray()
        );

        if (!$reservationRequest->room_id) {
            throw new BadRequestHttpException('Missing mandatory field room_id');
        }
        try {
            $room = $this->roomService->getOneById($reservationRequest->room_id);
        } catch (TypeError) {
            throw new NotFoundHttpException('Invalid room_id, room with ID ' . $reservationRequest->room_id . ' not found');
        }


        $reservation = new Reservation();
        $reservation->setApproved($reservationRequest->is_approved);
        $reservation->setTimeFrom($reservationRequest->time_from);
        $reservation->setTimeTo($reservationRequest->time_to);
        $reservation->setAuthor($author);
        $reservation->setResponsibleUser($responsibleUser);
        foreach ($invitedUsers as $invitedUser) {
            $reservation->addInvitedUser($invitedUser);
        }
        $reservation->setRoom($room);

        $validationErrors = $this->validator->validate($reservation);
        if (count($validationErrors) > 0) {
            throw new BadRequestHttpException('Invalid reservation object. Reason: ' . $validationErrors->get(0)->getMessage());
        }

        return $this->create($reservation);
    }

    public function getAllByApiQueries(array $queries): array
    {
        return $this->reservationRepository->findAllByApiQueries($queries);
    }

    public function getAllByAuthorOrResponsible(User $user): array
    {
        return $this->reservationRepository->findAllByAuthorOrResponsible($user);
    }

    public function getAllByInvitee(User $user): array
    {
        return $this->reservationRepository->findAllByApiQueries(['invited' => $user->getId()]);
    }

    public function getAllGroupedForManager(User $user, bool $isAdmin): array
    {
        $allReservations = $this->reservationRepository->findAll();

        /** @var Reservation[] $manageableReservations */
        if ($isAdmin) {
            $manageableReservations = $allReservations;
        } else {
            $manageableReservations = array_values(
                array_filter(
                    $allReservations,
                    fn($reservation) => $this->roomService->isTransitiveManagerOf($user, $reservation->getRoom())
                )
            );
        }

        $toApprove = [];
        $approvedCurrent = [];
        $approvedComing = [];
        $approvedPast = [];
        foreach ($manageableReservations as $reservation) {
            if (!$reservation->isApproved()) {
                $toApprove[] = $reservation;
            } elseif ($this->isCurrent($reservation)) {
                $approvedCurrent[] = $reservation;
            } elseif ($this->isComing($reservation)) {
                $approvedComing[] = $reservation;
            } elseif ($this->isPast($reservation)) {
                $approvedPast[] = $reservation;
            }
        }

        return [
            'toApprove' => $toApprove,
            'approvedCurrent' => $approvedCurrent,
            'approvedComing' => $approvedComing,
            'approvedPast' => $approvedPast,
        ];
    }

    public function getOneById(int $id): Reservation
    {
        $reservation = $this->reservationRepository->find($id);
        if (!$reservation) {
            throw new NotFoundHttpException('Reservation with ID ' . $id . ' not found');
        }
        return $reservation;
    }

    public function update(Reservation $reservation): Reservation
    {
        return $this->reservationRepository->createOrUpdate($reservation);
    }

    public function approveById(int $id): Reservation
    {
        $reservation = $this->getOneById($id);
        $reservation->setApproved(true);
        return $this->reservationRepository->createOrUpdate($reservation);
    }

    public function addInvitedUserById(int $reservationId, int $invitedUserId): Reservation
    {
        $reservation = $this->getOneById($reservationId);
        $invitedUser = $this->userService->getOneById($invitedUserId);
        $reservation->addInvitedUser($invitedUser);
        return $this->reservationRepository->createOrUpdate($reservation);
    }

    public function removeInvitedUserById(int $reservationId, int $invitedUserId): Reservation
    {
        $reservation = $this->getOneById($reservationId);
        if ($reservation->getResponsibleUser()->getId() === $invitedUserId) {
            throw new BadRequestHttpException('Cannot remove the responsible user from the list of invited users');
        }
        $invitedUser = $this->userService->getOneById($invitedUserId);
        if (!in_array($invitedUser, $reservation->getInvitedUsers())) {
            throw new NotFoundHttpException('User with ID ' . $invitedUserId . ' not found in the list of invited users');
        }
        $reservation->removeInvitedUser($invitedUser);
        return $this->reservationRepository->createOrUpdate($reservation);
    }

    public function deleteById(int $id): void
    {
        $reservation = $this->reservationRepository->find($id);
        if (!$reservation) {
            throw new NotFoundHttpException('Reservation with ID ' . $id . ' not found');
        }
        $this->reservationRepository->delete($reservation);
    }
}

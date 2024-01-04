<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\User;
use App\Form\Type\ReservationType;
use App\Service\ReservationService;
use App\Service\RoomService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED')]
class ReservationController extends AbstractController
{

    private ReservationService $reservationService;
    private RoomService $roomService;
    private UserService $userService;

    public function __construct(ReservationService $reservationService, RoomService $roomService, UserService $userService)
    {
        $this->reservationService = $reservationService;
        $this->roomService = $roomService;
        $this->userService = $userService;
    }

    #[Route('/reservations/new', name: 'app_book')]
    public function new(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $roomId = $request->query->get('room');
        $room = $this->roomService->getOneById($roomId);

        if (!$this->roomService->isBookableBy($room, $user) && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Tuto soukromou učebnu si nemůžete rezervovat, protože nejste jejím uživatelem ani správcem!');
        }
        $isManager = $this->isGranted('ROLE_ADMIN') || $this->roomService->isTransitiveManagerOf($user, $room);

        $reservation = new Reservation($room, $user, $isManager);
        $form = $this->createForm(ReservationType::class, $reservation, [
            'action' => $this->generateUrl('app_book') . '?room=' . $roomId,
            'actionType' => 'new',
            'isManager' => $isManager,
            'responsibleChoices' => $this->userService->getUserChoices(),
            'inviteChoices' => $this->userService->getUserChoices(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->reservationService->create($reservation);
            return $this->redirectToRoute('app_reservations_my');
        }

        return $this->render(
            'reservation.html.twig',
            [
                'room' => $room,
                'form' => $form,
            ]
        );
    }

    #[Route('/reservations/my', name: 'app_reservations_my')]
    public function my(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render(
            'my-reservations.html.twig',
            [
                'myReservations' => $this->reservationService->getAllByAuthorOrResponsible($user),
                'invitations' => $this->reservationService->getAllByInvitee($user),
            ]
        );
    }

    #[Route('/reservations/managed', name: 'app_reservations_managed')]
    public function managed(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $managedReservations = $this->reservationService->getAllGroupedForManager($user, $this->isGranted('ROLE_ADMIN'));
        return $this->render(
            'managed-reservations.html.twig',
            [
                'managedReservations' => $managedReservations,
            ]
        );
    }

    #[Route('reservations/{reservationId}/approve', name: 'app_reservations_approve')]
    public function approve(int $reservationId): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $reservation = $this->reservationService->getOneById($reservationId);
        $room = $reservation->getRoom();
        if (!$this->roomService->isTransitiveManagerOf($user, $room) && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Tuto žádost nemůžete schválit, protože nejste správcem dotyčné učebny!');
        }

        $this->reservationService->approveById($reservationId);
        return $this->redirectToRoute('app_reservations_managed');
    }

    #[Route('reservations/{reservationId}/edit', name: 'app_reservation_edit')]
    public function edit(int $reservationId, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $reservation = $this->reservationService->getOneById($reservationId);
        if ($reservation->getAuthor() !== $user && $reservation->getResponsibleUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Tuto rezervaci nemůžete editovat, protože nejste jejím autorem, ani není vedená na vaše jméno!');
        }

        $form = $this->createForm(ReservationType::class, $reservation, [
            'actionType' => 'edit',
            'isManager' => $this->roomService->isTransitiveManagerOf($user, $reservation->getRoom()) || $this->isGranted('ROLE_ADMIN'),
            'responsibleChoices' => $this->userService->getUserChoices(),
            'inviteChoices' => $this->userService->getUserChoices(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->reservationService->update($reservation);
            return $this->redirectToRoute('app_reservations_my');
        }

        return $this->render(
            'reservation.html.twig',
            [
                'room' => $reservation->getRoom(),
                'form' => $form,
            ]
        );
    }

}

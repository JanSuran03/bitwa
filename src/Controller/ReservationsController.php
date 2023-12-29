<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\User;
use App\Form\Type\ReservationType;
use App\Service\ReservationService;
use App\Service\RoomService;
use App\Service\UserService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED')]
class ReservationsController extends AbstractController
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

        if (!$this->roomService->isBookableBy($room, $user)) {
            throw $this->createAccessDeniedException('Tuto soukromou učebnu si nemůžete rezervovat, protože nejste jejím uživatelem ani správcem!');
        }

        $reservation = new Reservation($room, $user);
        $form = $this->createForm(ReservationType::class, $reservation, [
            'choices' => $this->userService->getInviteChoices($user),
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
                'myReservations' => $this->reservationService->getAllByAuthor($user),
                'invitations' => $this->reservationService->getAllByInvitee($user),
            ]
        );
    }

    #[Route('/reservations/managed', name: 'app_reservations_managed')]
    public function managed(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $managedReservations = $this->reservationService->getAllByManager($user);
        return $this->render(
            'managed-reservations.html.twig',
            [
                'managedReservations' => $managedReservations,
            ]
        );
    }

    #[Route('reservations/{reservationId}/approve', name: 'app_reservations_approve')]
    public function approve(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $reservationId = $request->attributes->get('reservationId');
        $reservation = $this->reservationService->getOneById($reservationId);
        $room = $reservation->getRoom();
        if (!$this->roomService->isTransitiveManagerOf($user, $room)) {
            throw $this->createAccessDeniedException('Tuto žádost nemůžete schválit, protože nejste správcem dotyčné místnosti!');
        }

        $this->reservationService->approveById($reservationId);
        return $this->redirectToRoute('app_reservations_managed');
    }

}

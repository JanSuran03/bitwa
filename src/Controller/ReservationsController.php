<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\User;
use App\Form\Type\ReservationType;
use App\Service\ReservationService;
use App\Service\RoomService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED')]
class ReservationsController extends AbstractController {

    private ReservationService $reservationService;
    private RoomService $roomService;

    public function __construct(ReservationService $reservationService, RoomService $roomService) {
        $this->reservationService = $reservationService;
        $this->roomService = $roomService;
    }

    #[Route('/reservations/new', name: 'app_book')]
    public function new(Request $request): Response {
        $roomId =$request->query->get('room');
        $room = $this->roomService->getOneById($roomId);

        /** @var User $user */
        $user = $this->getUser();

        if (! $room->isPublic() && ! $room->getMembers()->contains($user)) {
            throw $this->createAccessDeniedException('Tuto soukromou učebnu si nemůžete rezervovat, protože nejste jejím uživatelem!');
        }

        $reservation = new Reservation();
        $reservation->setIsApproved(false);
        $reservation->setRoom($room);
        $reservation->setAuthor($user);
        date_default_timezone_set('Europe/Prague');
        $reservation->setTimeFrom(new DateTime('now'));
        $reservation->setTimeTo(new DateTime('now'));

        $form = $this->createForm(ReservationType::class, $reservation);

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
    public function my(): Response {
        /** @var User $user */
        $user = $this->getUser();
        $myReservations = $this->reservationService->getAllByAuthor($user);
        return $this->render(
            'my-reservations.html.twig',
            [
                'myReservations' => $myReservations,
            ]
        );
    }

    #[Route('/reservations/managed', name: 'app_reservations_managed')]
    public function managed(): Response {
        return $this->render('managed-reservations.html.twig');
    }

}

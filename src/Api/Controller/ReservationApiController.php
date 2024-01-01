<?php

namespace App\Api\Controller;

use App\Api\DTO\ReservationRequest;
use App\Api\DTO\ReservationResponse;
use App\Entity\Reservation;
use App\Service\ReservationService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class ReservationApiController extends AbstractFOSRestController
{
    private ReservationService $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    #[View]
    #[ParamConverter('reservationRequest', class: 'App\Api\DTO\ReservationRequest', converter: 'fos_rest.request_body')]
    #[Post("/reservations")]
    public function new(ReservationRequest $reservationRequest): ReservationResponse
    {
        $reservation = $this->reservationService->createFromRequestDto($reservationRequest);
        return ReservationResponse::fromEntity($reservation);
    }

    #[View]
    #[Get("/reservations")]
    public function list(Request $request): array
    {
        $reservations = $this->reservationService->getAllByApiQueries($request->query->all());
        return array_map(
            fn(Reservation $reservation) => ReservationResponse::fromEntity($reservation),
            $reservations
        );
    }

    #[View]
    #[ParamConverter('reservation', class: 'App\Entity\Reservation')]
    #[Get("/reservations/{id}")]
    public function detail(Reservation $reservation): ReservationResponse
    {
        return ReservationResponse::fromEntity($reservation);
    }

    #[View]
    #[Put("/reservations/{id}/approve")]
    public function approve(int $id): ReservationResponse
    {
        $reservation = $this->reservationService->approveById($id);
        return ReservationResponse::fromEntity($reservation);
    }

    #[View]
    #[Put("/reservations/{reservationId}/invited-users/{invitedUserId}")]
    public function addInvitedUser(int $reservationId, int $invitedUserId): ReservationResponse
    {
        $reservation = $this->reservationService->addInvitedUserById($reservationId, $invitedUserId);
        return ReservationResponse::fromEntity($reservation);
    }

    #[View]
    #[Delete("/reservations/{reservationId}/invited-users/{invitedUserId}")]
    public function removeInvitedUser(int $reservationId, int $invitedUserId): void
    {
        $this->reservationService->removeInvitedUserById($reservationId, $invitedUserId);
    }

    #[View]
    #[Delete("/reservations/{id}")]
    public function delete(int $id): void
    {
        $this->reservationService->deleteById($id);
    }
}
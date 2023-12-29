<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\Room;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use function Symfony\Component\Clock\now;

class ReservationService {
    private EntityRepository $reservationRepository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->reservationRepository = $entityManager->getRepository(Reservation::class);
    }

    public function getAll(): array {
        return $this->reservationRepository->findAll();
    }

    public function getAllByAuthor(User $user): array {
        return $this->reservationRepository->findBy(['author' => $user]);
    }

    public function getOneById(int $id): Room {
        return $this->reservationRepository->find($id);
    }

    public function approveById(int $id) {
        // TODO
    }

    public function create(Reservation $reservation) {
        $this->reservationRepository->create($reservation);
    }


}

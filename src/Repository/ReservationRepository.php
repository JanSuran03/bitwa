<?php

namespace App\Repository;

use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 *
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function createOrUpdate(Reservation $reservation): Reservation
    {
        $this->getEntityManager()->persist($reservation);
        $this->getEntityManager()->flush();

        return $reservation;
    }

    public function findAllByApiQueries(array $queries): array
    {
        $queryBuilder = $this->createQueryBuilder('r');

        foreach ($queries as $key => $value) {
            switch ($key) {
                case 'approved':
                    if ($value === 'true') {
                        $boolValue = true;
                    } elseif ($value === 'false') {
                        $boolValue = false;
                    } else {
                        break;
                    }
                    $queryBuilder
                        ->andWhere('r.isApproved = :approved')
                        ->setParameter('approved', $boolValue);
                    break;
                case 'author':
                    $queryBuilder
                        ->andWhere('r.author = :authorId')
                        ->setParameter('authorId', $value);
                    break;
                case 'responsible':
                    $queryBuilder
                        ->andWhere('r.responsibleUser = :responsibleUserId')
                        ->setParameter('responsibleUserId', $value);
                    break;
                case 'invited':
                    $queryBuilder
                        ->andWhere(':invitedUserId MEMBER OF r.invitedUsers')
                        ->setParameter('invitedUserId', $value);
                    break;
                case 'room':
                    $queryBuilder
                        ->andWhere('r.room = :roomId')
                        ->setParameter('roomId', $value);
                    break;
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function findAllByAuthorOrResponsible(User $user): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.author = :user OR r.responsibleUser = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function delete(Reservation $reservation): void
    {
        $this->getEntityManager()->remove($reservation);
        $this->getEntityManager()->flush();
    }
}

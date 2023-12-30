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

    public function create(Reservation $reservation): Reservation
    {
        $em = $this->getEntityManager();
        $em->persist($reservation);
        $em->flush();

        return $reservation;
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function findByAuthorOrResponsible(User $user): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.author = :user OR r.responsible_user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?Reservation
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

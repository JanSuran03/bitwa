<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function createOrUpdate(User $user): User
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        return $user;
    }

    public function findByApiQueries(array $queries): array
    {
        $queryBuilder = $this->createQueryBuilder('u');

        foreach ($queries as $key => $value) {
            switch ($key) {
                case 'name':
                    $queryBuilder
                        ->andWhere('u.name LIKE :name')
                        ->setParameter('name', '%' . $value . '%');
                    break;
                case 'email':
                    $queryBuilder
                        ->andWhere('u.email LIKE :email')
                        ->setParameter('email', '%' . $value . '%');
                    break;
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function delete(User $user): void
    {
        $this->getEntityManager()->remove($user);
        $this->getEntityManager()->flush();
    }
}

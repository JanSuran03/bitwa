<?php

namespace App\Repository;

use App\Entity\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Group>
 *
 * @method Group|null find($id, $lockMode = null, $lockVersion = null)
 * @method Group|null findOneBy(array $criteria, array $orderBy = null)
 * @method Group[]    findAll()
 * @method Group[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

    public function createOrUpdate(Group $group): Group
    {
        $this->getEntityManager()->persist($group);
        $this->getEntityManager()->flush();

        return $group;
    }

    public function findAllByApiQueries(array $queries): array
    {
        $queryBuilder = $this->createQueryBuilder('r');

        foreach ($queries as $key => $value) {
            switch ($key) {
                case 'name':
                    $queryBuilder
                        ->andWhere('r.name LIKE :name')
                        ->setParameter('name', '%' . $value . '%');
                    break;
                case 'parent':
                    $queryBuilder
                        ->andWhere('r.parent = :parentId')
                        ->setParameter('parentId', $value);
                    break;
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function delete(Group $group): void
    {
        $this->getEntityManager()->remove($group);
        $this->getEntityManager()->flush();
    }
}

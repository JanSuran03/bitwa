<?php

namespace App\Repository;

use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Room>
 *
 * @method Room|null find($id, $lockMode = null, $lockVersion = null)
 * @method Room|null findOneBy(array $criteria, array $orderBy = null)
 * @method Room[]    findAll()
 * @method Room[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    public function createOrUpdate(Room $room): Room
    {
        $this->getEntityManager()->persist($room);
        $this->getEntityManager()->flush();

        return $room;
    }

    public function findAllByApiQueries(array $queries): array
    {
        $queryBuilder = $this->createQueryBuilder('r');

        foreach ($queries as $key => $value) {
            switch ($key) {
                case 'building':
                    $queryBuilder
                        ->andWhere('r.building LIKE :building')
                        ->setParameter('building', '%' . $value . '%');
                    break;
                case 'name':
                    $queryBuilder
                        ->andWhere('r.name LIKE :name')
                        ->setParameter('name', '%' . $value . '%');
                    break;
                case 'group':
                    $queryBuilder
                        ->andWhere('r.group = :groupId')
                        ->setParameter('groupId', $value);
                    break;
                case 'public':
                    if ($value === 'true') {
                        $boolValue = true;
                    } elseif ($value === 'false') {
                        $boolValue = false;
                    } else {
                        break;
                    }
                    $queryBuilder
                        ->andWhere('r.isPublic = :public')
                        ->setParameter('public', $boolValue);
                    break;
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function delete(Room $room): void
    {
        $this->getEntityManager()->remove($room);
        $this->getEntityManager()->flush();
    }
}

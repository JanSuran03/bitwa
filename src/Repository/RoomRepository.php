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
class RoomRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Room::class);
    }

    public function findByApiQueries(array $queries): array
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

    public function findPublicRooms(): array {
        return $this->findBy(["isPublic" => true]);
    }

    public function findByNameAndBuilding(string $name, string $building): ?Room {
        return $this->findOneBy([
            "name" => $name,
            "building" => $building
        ]);
    }

    public function createRoom(string $name, string $building, bool $isPublic): Room {
        $room = new Room();
        $room->setName($name);
        $room->setBuilding($building);
        $room->setPublic($isPublic);
        $room->setLocked(true);

        $this->_em->persist($room);
        $this->_em->flush();

        return $room;
    }

    public function setRoom(Room $room): void {
        $this->_em->persist($room);
        $this->_em->flush();
    }
}

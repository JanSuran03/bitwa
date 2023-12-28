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
}

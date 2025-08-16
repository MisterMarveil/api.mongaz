<?php
namespace App\Repository;

use App\Entity\DriverProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DriverProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DriverProfile::class);
    }

    public function findAvailableNearby(float $lat, float $lon, int $radiusKm = 5): array
    {
        // simplistic geo filter, replace with Haversine for production
        return $this->createQueryBuilder('d')
            ->andWhere('d.available = true')
            ->andWhere('ABS(d.currentLat - :lat) < 0.1')
            ->andWhere('ABS(d.currentLon - :lon) < 0.1')
            ->setParameter('lat', $lat)
            ->setParameter('lon', $lon)
            ->getQuery()
            ->getResult();
    }
}

<?php
namespace App\Repository;

use App\Domain\Activity\Entity\ActivityLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ActivityLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActivityLog::class);
    }

    public function findByDriverAndPeriod(int $driverId, \DateTimeInterface $from, \DateTimeInterface $to): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.driver = :driver')
            ->andWhere('a.createdAt BETWEEN :from AND :to')
            ->setParameter('driver', $driverId)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->getQuery()
            ->getResult();
    }
}

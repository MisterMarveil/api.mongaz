<?php
namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findPendingOrders(): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.status = :status')
            ->setParameter('status', 'AWAITING_ASSIGNMENT')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find activity logs (orders) for a specific driver
     *
     * @param int $driverId
     * @param \DateTimeImmutable|null $from
     * @param \DateTimeImmutable|null $to
     * @return array
     */
    public function findLogsForDriver(int $driverId, ?\DateTimeImmutable $from = null, ?\DateTimeImmutable $to = null): array
    {
        $qb = $this->createQueryBuilder('o')
            ->andWhere('o.driver = :driverId')
            ->setParameter('driverId', $driverId)
            ->orderBy('o.createdAt', 'DESC');

        if ($from) {
            $qb->andWhere('o.createdAt >= :from')
               ->setParameter('from', $from->format('Y-m-d 00:00:00'));
        }

        if ($to) {
            $qb->andWhere('o.createdAt <= :to')
               ->setParameter('to', $to->format('Y-m-d 23:59:59'));
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Compute KPIs (total orders, completed, canceled) for a given date range
     *
     * @param \DateTimeImmutable $from
     * @param \DateTimeImmutable $to
     * @return array
     */
    public function computeKpis(?int $driverId, \DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "
            SELECT 
                COUNT(*) as total_orders,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'canceled' THEN 1 ELSE 0 END) as canceled
            FROM orders
            WHERE created_at BETWEEN :from AND :to
        ";

        $params = [
            'from' => $from->format('Y-m-d 00:00:00'),
            'to'   => $to->format('Y-m-d 23:59:59'),
        ];

        if ($driverId !== null) {
            $sql .= " AND driver_id = :driverId";
            $params['driverId'] = $driverId;
        }

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery($params)->fetchAssociative();

        return [
            'total_orders' => (int) ($result['total_orders'] ?? 0),
            'completed'    => (int) ($result['completed'] ?? 0),
            'canceled'     => (int) ($result['canceled'] ?? 0),
        ];
    }
}


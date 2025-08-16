<?php

namespace App\Domain\Activity\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use App\Domain\User\Entity\DriverProfile;
use App\Repository\ActivityLogRepository;

#[ORM\Entity(repositoryClass: ActivityLogRepository::class)]
#[ApiResource]
#[ORM\UniqueConstraint(name: 'driver_date_unique', columns: ['driver_id', 'date'])]
class ActivityLog
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: DriverProfile::class)]
    private DriverProfile $driver;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $date;

    #[ORM\Column(type: 'integer')]
    private int $ordersCount = 0;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $amountSum = '0.00';

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getDriver(): DriverProfile
    {
        return $this->driver;
    }

    public function setDriver(DriverProfile $driver): self
    {
        $this->driver = $driver;
        return $this;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getOrdersCount(): int
    {
        return $this->ordersCount;
    }

    public function setOrdersCount(int $ordersCount): self
    {
        $this->ordersCount = $ordersCount;
        return $this;
    }

    public function getAmountSum(): string
    {
        return $this->amountSum;
    }

    public function setAmountSum(string $amountSum): self
    {
        $this->amountSum = $amountSum;
        return $this;
    }
}

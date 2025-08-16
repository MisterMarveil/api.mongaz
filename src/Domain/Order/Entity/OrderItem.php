<?php
namespace App\Domain\Order\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use App\Repository\OrderItemRepository;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
#[ApiResource]
class OrderItem
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['order:read', 'order:write'])]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'items')]
    private Order $order;

    #[ORM\Column(length: 50)]
    #[Groups(['order:read', 'order:write'])]
    private string $brand;

    #[ORM\Column(length: 20)]
    #[Groups(['order:read', 'order:write'])]
    private string $capacity;

    #[ORM\Column(type: 'integer')]
    #[Groups(['order:read', 'order:write'])]
    private int $quantity;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): self
    {
        $this->order = $order;
        return $this;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;
        return $this;
    }

    public function getCapacity(): string
    {
        return $this->capacity;
    }

    public function setCapacity(string $capacity): self
    {
        $this->capacity = $capacity;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }
}

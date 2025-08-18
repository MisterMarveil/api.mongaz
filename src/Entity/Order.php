<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\DriverProfile;
use App\Entity\User;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model;
use App\Action\Admin\AssignDriver;
use App\Action\Admin\CancelOrder;
use App\Action\Admin\SuggestedDrivers;
use App\Action\Admin\Kpis;
use App\Action\Admin\ActivityLogs;
use App\Repository\OrderRepository;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ApiResource(
    openapiContext: ['security' => [['JWT' => []]]],    
    operations: [
        new Post(
            name: 'admin_assign_driver',
            uriTemplate: '/admin/orders/{id}/assign-driver',
            controller: AssignDriver::class,
            openapi: new Model\Operation(
                summary: 'Assign a driver to an order',
                description: 'Admin manually assigns a driver.',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => ['driver_id' => ['type' => 'integer']],
                                'required' => ['driver_id']
                            ],
                            'example' => ['driver_id' => 45]
                        ]
                    ])
                ),
                responses: [
                    '200' => new Model\Response(description: 'Driver assigned'),
                    '404' => new Model\Response(description: 'Order or driver not found')
                ]
                ),
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Post(
            name: 'admin_cancel_order',
            uriTemplate: '/admin/orders/{id}/cancel',
            controller: CancelOrder::class,
            openapi: new Model\Operation(
                summary: 'Cancel an order',
                description: 'Admin cancels an order with reason.',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => ['reason' => ['type' => 'string']],
                            ],
                            'example' => ['reason' => 'Stock unavailable']
                        ]
                    ])
                ),
                responses: [
                    '200' => new Model\Response(description: 'Order canceled'),
                    '404' => new Model\Response(description: 'Order not found')
                ]
            ),
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Get(
            name: 'admin_suggested_drivers',
            uriTemplate: '/admin/orders/{id}/suggested-drivers',
            controller: SuggestedDrivers::class,
            openapi:  new Model\Operation(
                summary: 'Get suggested drivers',
                description: 'Find nearby available drivers for an order.',
                responses: [
                    '200' => new Model\Response(
                        description: 'List of suggested drivers',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'id' => ['type' => 'integer'],
                                            'name' => ['type' => 'string'],
                                            'lat' => ['type' => 'number'],
                                            'lon' => ['type' => 'number']
                                        ]
                                    ]
                                ]
                            ]
                        ])
                    ),
                    '404' => new Model\Response(description: 'Order not found')
                ]
            ),
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Get(
            name: 'admin_kpis',
            uriTemplate: '/admin/kpis',
            controller: Kpis::class,
             openapi: new Model\Operation(
                 summary: 'KPIs',
                description: 'Retrieve system-wide KPIs for orders.',
                parameters: [
                    ['name' => 'from', 'in' => 'query', 'required' => true, 'schema' => ['type' => 'string', 'format' => 'date']],
                    ['name' => 'to', 'in' => 'query', 'required' => true, 'schema' => ['type' => 'string', 'format' => 'date']]
                ],
                responses: [
                    '200' => new Model\Response(
                        description: 'KPIs data',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'total_orders' => ['type' => 'integer'],
                                        'completed' => ['type' => 'integer'],
                                        'canceled' => ['type' => 'integer']
                                    ]
                                ]
                            ]
                        ])
                    )
                ]
             ),
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Get(
            name: 'admin_activity_logs',
            uriTemplate: '/admin/activity-logs',
            controller: ActivityLogs::class,
            openapi: new Model\Operation(
                summary: 'Driver activity logs',
                description: 'Fetch activity logs for drivers within a period.',
                parameters: [
                    ['name' => 'driver_id', 'in' => 'query', 'required' => true, 'schema' => ['type' => 'integer']],
                    ['name' => 'period', 'in' => 'query', 'required' => false, 'schema' => ['type' => 'string']]
                ],
                responses: [
                    '200' => new Model\Response(description: 'Logs retrieved'),
                    '404' => new Model\Response(description: 'Driver not found')
                ]
            ),
            security: "is_granted('ROLE_ADMIN')"
        )        
    ],
    normalizationContext: ['groups' => ['order:read']],
    denormalizationContext: ['groups' => ['order:write']]
)]
#[ApiFilter(SearchFilter::class, properties: ['status' => 'exact', 'assignedDriver' => 'exact', 'customerPhone' => 'partial'])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt' => 'DESC'])]
class Order
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['order:read'])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Groups(['order:read', 'order:write'])]
    private string $customerPhone;

    #[ORM\Column(length: 120, nullable: true)]
    #[Groups(['order:read', 'order:write'])]
    private ?string $customerName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['order:read', 'order:write'])]
    private string $address;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups(['order:read', 'order:write'])]
    private string $amount = '0.00';

    #[ORM\Column(length: 40)]
    #[Groups(['order:read'])]
    private string $status = 'AWAITING_ASSIGNMENT';

    #[ORM\ManyToOne(targetEntity: DriverProfile::class)]
    #[Groups(['order:read'])]
    private ?DriverProfile $assignedDriver = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[Groups(['order:read'])]
    private User $createdBy;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, cascade: ['persist'], orphanRemoval: true)]
    #[Groups(['order:read', 'order:write'])]
    private Collection $items;

    #[ORM\Column(length: 64, nullable: true, unique: true)]
    private ?string $idempotencyKey = null;

    #[ORM\Column]
    #[Groups(['order:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private \DateTime $updatedAt;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getCustomerPhone(): string
    {
        return $this->customerPhone;
    }

    public function setCustomerPhone(string $customerPhone): self
    {
        $this->customerPhone = $customerPhone;
        return $this;
    }

    public function getCustomerName(): ?string
    {
        return $this->customerName;
    }

    public function setCustomerName(?string $customerName): self
    {
        $this->customerName = $customerName;
        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getAssignedDriver(): ?DriverProfile
    {
        return $this->assignedDriver;
    }

    public function setAssignedDriver(?DriverProfile $assignedDriver): self
    {
        $this->assignedDriver = $assignedDriver;
        return $this;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): self
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * @return Collection|OrderItem[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setOrder($this);
        }
        return $this;
    }

    public function removeItem(OrderItem $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getOrder() === $this) {
                $item->setOrder(null);
            }
        }
        return $this;
    }

    public function getIdempotencyKey(): ?string
    {
        return $this->idempotencyKey;
    }

    public function setIdempotencyKey(?string $idempotencyKey): self
    {
        $this->idempotencyKey = $idempotencyKey;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}

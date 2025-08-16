<?php
namespace App\Domain\User\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use App\Action\Driver\AcceptOrder;
use App\Action\Driver\StartDelivery;
use App\Action\Driver\ConfirmDelivery;
use App\Action\Driver\CancelDelivery;
use App\Action\Driver\UpdateLocation;
use App\Action\Driver\UpdateAvailability;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use App\Repository\DriverProfileRepository;

#[ORM\Entity(repositoryClass: DriverProfileRepository::class)]
#[ApiResource(
    operations: [
       new Post(
            name: 'driver_accept_order',
            uriTemplate: '/drivers/current/accept',
            controller: AcceptOrder::class,
            openapi: new Model\Operation(
                summary: 'Accept an order',
                description: 'Driver accepts an available order.',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'order_id' => ['type' => 'integer']
                                ],
                                'required' => ['order_id']
                            ],
                            'example' => ['order_id' => 123]
                        ]
                    ])
                ),
                responses: [
                    '200' => new Model\Response(
                        description: 'Order accepted',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'status' => ['type' => 'string'],
                                        'order_id' => ['type' => 'integer']
                                    ]
                                ]
                            ]
                        ])
                    ),
                    '400' => new Model\Response(description: 'Order not available'),
                    '404' => new Model\Response(description: 'Order not found')
                ]
            ),
            security: "is_granted('ROLE_DRIVER')"
        ),
       new Post(
            name: 'driver_start_delivery',
            uriTemplate: '/drivers/current/start-delivery',
            controller: StartDelivery::class,
            openapi: new Model\Operation(
                summary: 'Start delivery',
                description: 'Driver marks the order as in progress.',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => ['order_id' => ['type' => 'integer']],
                                'required' => ['order_id']
                            ],
                            'example' => ['order_id' => 123]
                        ]
                    ])
                ),
                responses: [
                    '200' => new Model\Response(description: 'Delivery started'),
                    '403' => new Model\Response(description: 'Not allowed')
                ]
            ),
            security: "is_granted('ROLE_DRIVER')"
        ),
       new Post(
            name: 'driver_confirm_delivery',
            uriTemplate: '/drivers/current/confirm-delivery',
            controller: ConfirmDelivery::class,
            openapi: new Model\Operation(
                summary: 'Confirm delivery',
                description: 'Driver confirms order has been delivered.',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => ['order_id' => ['type' => 'integer']],
                                'required' => ['order_id']
                            ],
                            'example' => ['order_id' => 123]
                        ]
                    ])
                ),
                responses: [
                    '200' => new Model\Response(description: 'Delivery confirmed'),
                    '403' => new Model\Response(description: 'Not allowed')
                ]
            ),
            security: "is_granted('ROLE_DRIVER')"
        ),
        new Post(
            name: 'driver_cancel_delivery',
            uriTemplate: '/drivers/current/initiate-cancel',
            controller: CancelDelivery::class,
            openapi: new Model\Operation(
                summary: 'Cancel delivery',
                description: 'Driver cancels a delivery in progress.',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'order_id' => ['type' => 'integer'],
                                    'reason' => ['type' => 'string']
                                ],
                                'required' => ['order_id']
                            ],
                            'example' => ['order_id' => 123, 'reason' => 'Customer not available']
                        ]
                    ])
                ),
                responses: [
                    '200' => new Model\Response(description: 'Delivery canceled'),
                    '403' => new Model\Response(description: 'Not allowed')
                ]
            ),
            security: "is_granted('ROLE_DRIVER')"
        ),
        new Post(
            name: 'driver_update_location',
            uriTemplate: '/drivers/current/location',
            controller: UpdateLocation::class,
            openapi: new Model\Operation(
                summary: 'Update driver location',
                description: 'Driver updates current GPS coordinates.',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'lat' => ['type' => 'number', 'format' => 'float'],
                                    'lon' => ['type' => 'number', 'format' => 'float']
                                ],
                                'required' => ['lat','lon']
                            ],
                            'example' => ['lat' => 3.8792, 'lon' => 11.5021]
                        ]
                    ])
                ),
                responses: [
                    '200' => new Model\Response(description: 'Location updated')
                ]
            ),
            security: "is_granted('ROLE_DRIVER')"
        ),
        new Post(
            name: 'driver_update_availability',
            uriTemplate: '/drivers/current/availability',
            controller: UpdateAvailability::class,
            openapi: new Model\Operation(
                summary: 'Update availability',
                description: 'Driver sets availability status.',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => ['available' => ['type' => 'boolean']],
                                'required' => ['available']
                            ],
                            'example' => ['available' => true]
                        ]
                    ])
                ),
                responses: [
                    '200' => new Model\Response(description: 'Availability updated')
                ]
            ),        
            security: "is_granted('ROLE_DRIVER')"
        )
    ],
    normalizationContext: ['groups' => ['driver:read']],
    denormalizationContext: ['groups' => ['driver:write']]
)]
class DriverProfile
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['driver:read'])]
    private ?Uuid $id = null;

    #[ORM\OneToOne(targetEntity: User::class, cascade: ['persist'], inversedBy: 'driverProfile')]
    #[Groups(['driver:read', 'driver:write'])]
    private User $user;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['driver:read', 'driver:write'])]
    private ?string $vehicle = null;

    #[ORM\Column]
    #[Groups(['driver:read', 'driver:write'])]
    private bool $available = true;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    #[Groups(['driver:read'])]
    private ?string $currentLat = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    #[Groups(['driver:read'])]
    private ?string $currentLon = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['driver:read'])]
    private ?\DateTimeImmutable $lastSeenAt = null;

    public function getId(): ?Uuid { return $this->id; }
    public function getUser(): User { return $this->user; }
    public function setUser(User $user): self { $this->user = $user; return $this; }

    public function getVehicle(): ?string { return $this->vehicle; }
    public function setVehicle(?string $vehicle): self { $this->vehicle = $vehicle; return $this; }

    public function isAvailable(): bool { return $this->available; }
    public function setAvailable(bool $available): self { $this->available = $available; return $this; }

    public function getCurrentLat(): ?string { return $this->currentLat; }
    public function setCurrentLat(?string $lat): self { $this->currentLat = $lat; return $this; }

    public function getCurrentLon(): ?string { return $this->currentLon; }
    public function setCurrentLon(?string $lon): self { $this->currentLon = $lon; return $this; }

    public function getLastSeenAt(): ?\DateTimeImmutable { return $this->lastSeenAt; }
    public function setLastSeenAt(?\DateTimeImmutable $time): self { $this->lastSeenAt = $time; return $this; }
}

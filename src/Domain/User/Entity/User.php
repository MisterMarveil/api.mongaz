<?php
namespace App\Domain\User\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\UserRepository;
use ApiPlatform\OpenApi\Model;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource(
    operations: [
         new Post(
            name: 'password_reset_request', 
            routeName: 'request_password_reset', 
            security: "is_granted('PUBLIC_ACCESS')",
            openapi: new Model\Operation(
                summary: 'Request a reset password code',
                description: 'Generate and send a reset password code by SMS to the given phone number.',
                requestBody: new Model\RequestBody(
                    required: true,
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'phone' => ['type' => 'string', 'example' => '+237691919116']
                                ],
                                'required' => ['phone']
                            ]
                        ]
                    ])
                ),
                responses: [
                    '200' => new Model\Response(
                        description: 'Reset code sent successfully',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'status' => ['type' => 'string', 'example' => 'reset code sent']
                                    ]
                                ]
                            ]
                        ])
                    ),
                    '400' => new Model\Response(
                        description: 'User not active',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'error' => ['type' => 'string', 'example' => 'user not activated']
                                    ]
                                ]
                            ]
                        ])
                    ),
                    '404' => new Model\Response(
                        description: 'User not found',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'error' => ['type' => 'string', 'example' => 'user not found']
                                    ]
                                ]
                            ]
                        ])
                    ),
                    '429' => new Model\Response(
                        description: 'Too many attempts',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'error' => ['type' => 'string', 'example' => 'too many attempts']
                                    ]
                                ]
                            ]
                        ])
                    )
                ]
            )
        ),
        new Post(
            name: 'password_reset', 
            routeName: 'reset_password', 
            security: "is_granted('PUBLIC_ACCESS')",
            openapi: new Model\Operation(
                summary: 'Reset password',
                description: 'Verify the provided code and reset the password.',
                requestBody: new Model\RequestBody(
                    required: true,
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'phone' => ['type' => 'string', 'example' => '+237691919116'],
                                    'code' => ['type' => 'string', 'example' => '123456'],
                                    'newPassword' => ['type' => 'string', 'example' => 'newSecretPass']
                                ],
                                'required' => ['phone', 'code', 'newPassword']
                            ]
                        ]
                    ])
                ),
                responses: [
                    '200' => new Model\Response(
                        description: 'Password successfully reset',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'status' => ['type' => 'string', 'example' => 'password reset successful']
                                    ]
                                ]
                            ]
                        ])
                    ),
                    '401' => new Model\Response(
                        description: 'Invalid or expired code',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'error' => ['type' => 'string', 'example' => 'invalid code']
                                    ]
                                ]
                            ]
                        ])
                    ),
                    '404' => new Model\Response(
                        description: 'User not found',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'error' => ['type' => 'string', 'example' => 'user not found']
                                    ]
                                ]
                            ]
                        ])
                    ),
                    '429' => new Model\Response(
                        description: 'Too many attempts',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'error' => ['type' => 'string', 'example' => 'too many attempts']
                                    ]
                                ]
                            ]
                        ])
                    )
                ]
            )
        ),
        new Post(
            name: 'verification', 
            routeName: 'verify_activation', 
            security: "is_granted('PUBLIC_ACCESS')",
            openapi: new Model\Operation(
                 summary: 'Verify and activate user account',
                 description: 'Check the activation code validity and activate user account.',
                 requestBody: new Model\RequestBody(
                     required: true,
                     content: new \ArrayObject([
                         'application/json' => [
                             'schema' => [
                                 'type' => 'object',
                                 'properties' => [
                                     'phone' => ['type' => 'string', 'example' => '+237691919116'],
                                     'code' => ['type' => 'string', 'example' => '654321']
                                  ],
                                  'required' => ['phone', 'code']
                             ]
                         ]
                     ])
                 ),
                 responses: [
                    '200' => new Model\Response(
                        description: 'Activation successful',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'status' => ['type' => 'string', 'example' => 'activated']
                                     ]
                                 ]
                             ]
                        ])
                    ),
                   '401' => new Model\Response(
                       description: 'Invalid code',
                       content: new \ArrayObject([
                          'application/json' => [
                              'schema' => [
                                  'type' => 'object',
                                  'properties' => [
                                     'error' => ['type' => 'string', 'example' => 'invalid code']
                                   ]
                               ]
                           ]
                       ])
                   ),
                   '404' => new Model\Response(
                        description: 'User not found',
                         content: new \ArrayObject([
                             'application/json' => [
                                 'schema' => [
                                     'type' => 'object',
                                     'properties' => [
                                         'error' => ['type' => 'string', 'example' => 'user not found']
                                      ]
                                  ]
                              ]
                          ])
                    ),
                   '410' => new Model\Response(
                        description: 'Code expired',
                        content: new \ArrayObject([
                            'application/json' => [
                                 'schema' => [
                                      'type' => 'object',
                                      'properties' => [
                                          'error' => ['type' => 'string', 'example' => 'activation code expired']
                                       ]
                                  ]
                             ] 
                        ])
                    ),
                    '429' => new Model\Response(
                         description: 'Too many attempts',
                         content: new \ArrayObject([
                              'application/json' => [
                                   'schema' => [
                                       'type' => 'object',
                                       'properties' => [
                                           'error' => ['type' => 'string', 'example' => 'too many attempts']
                                        ]
                                    ]
                               ]
                         ])
                    )
                ]
            )
        ),
        new Post(
            name: 'resend-code', 
            routeName: 'resend_activation', 
            security: "is_granted('PUBLIC_ACCESS')",
            openapi: new Model\Operation(
                summary: 'Resend activation code',
                description: 'Resend the activation code if still valid, or generate a new one and send.',
                requestBody: new Model\RequestBody(
                    required: true,
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'phone' => ['type' => 'string', 'example' => '+237691919116']
                                ],
                                'required' => ['phone']
                            ]
                        ]
                    ])
                ),
                responses: [
                    '200' => new Model\Response(
                        description: 'Code successfully resent',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'status' => ['type' => 'string', 'example' => 'code sent']
                                    ]
                                ]
                            ]
                        ])
                    ),
                    '400' => new Model\Response(
                        description: 'Account already activated',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'error' => ['type' => 'string', 'example' => 'already activated']
                                    ]
                                ]
                            ]
                        ])
                    ),
                    '404' => new Model\Response(
                        description: 'User not found',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'error' => ['type' => 'string', 'example' => 'user not found']
                                    ]
                                ]
                            ]
                        ])
                    ),
                    '429' => new Model\Response(
                        description: 'Too many attempts',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'error' => ['type' => 'string', 'example' => 'too many attempts']
                                    ]
                                ]
                            ]
                        ])
                    )
                ]
            )
        ),
        new Get(security: "is_granted('ROLE_ADMIN') or object == user"),
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
        new Post(security: "is_granted('ROLE_ADMIN')")
    ],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['user:read'])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 20, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 8, max: 20)]
    #[Groups(['user:read', 'user:write'])]
    private string $phone;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    #[Groups(['user:write'])]
    private string $password;

    #[ORM\Column(length: 120, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?bool $isActive = true;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?bool $isEnabled = false;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read'])]
    private ?\DateTimeImmutable $enabledAt = null;

    #[ORM\Column(length: 6, nullable: true)]
    private ?string $activationCode = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $activationCodeExpiresAt = null;

    #[ORM\Column(length: 6, nullable: true)]
    private ?string $resetPasswordCode = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $resetPasswordCodeExpiresAt = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
       $this->roles = ['ROLE_USER'];
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->phone;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void {}

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTime();
    }

      // --- Activation Flow ---
    public function getActivationCode(): ?string
    {
        return $this->activationCode;
    }

    public function setActivationCode(?string $code): self
    {
        $this->activationCode = $code;
        return $this;
    }

    public function getActivationCodeExpiresAt(): ?\DateTimeImmutable
    {
        return $this->activationCodeExpiresAt;
    }

    public function setActivationCodeExpiresAt(?\DateTimeImmutable $date): self
    {
        $this->activationCodeExpiresAt = $date;
        return $this;
    }

    public function activateAccount(): self
    {
        $this->isEnabled = true;
        $this->enabledAt = new \DateTimeImmutable();
        $this->activationCode = null;
        $this->activationCodeExpiresAt = null;
        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled === true;
    }

    // --- Reset Password Flow ---
    public function getResetPasswordCode(): ?string
    {
        return $this->resetPasswordCode;
    }

    public function setResetPasswordCode(?string $code): self
    {
        $this->resetPasswordCode = $code;
        return $this;
    }

    public function getResetPasswordCodeExpiresAt(): ?\DateTimeImmutable
    {
        return $this->resetPasswordCodeExpiresAt;
    }

    public function setResetPasswordCodeExpiresAt(?\DateTimeImmutable $date): self
    {
        $this->resetPasswordCodeExpiresAt = $date;
        return $this;
    }

    public function clearResetPasswordCode(): self
    {
        $this->resetPasswordCode = null;
        $this->resetPasswordCodeExpiresAt = null;
        return $this;
    }

    public function canResetPassword(string $code): bool
    {
        return $this->resetPasswordCode === $code
            && $this->resetPasswordCodeExpiresAt !== null
            && $this->resetPasswordCodeExpiresAt > new \DateTimeImmutable();
    } 
}

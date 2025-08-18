<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use App\Entity\User;
use App\Entity\Order;
use App\Repository\CommentRepository;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ApiResource(
    openapiContext: ['security' => [['JWT' => []]]],
    security: "is_granted('ROLE_USER')",
    normalizationContext: ['groups' => ['comment:read']],
    denormalizationContext: ['groups' => ['comment:write']]
)]
class Comment
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $author;

    #[ORM\ManyToOne(targetEntity: Order::class)]
    private Order $order;

    #[ORM\Column(type: 'text')]
    #[Groups(['comment:read', 'comment:write'])]
    private string $text;

    #[ORM\Column]
    #[Groups(['comment:read'])]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): self
    {
        $this->author = $author;
        return $this;
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

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;
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
}

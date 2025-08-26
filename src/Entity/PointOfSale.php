<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use App\Repository\PointOfSaleRepository;

#[ORM\Entity(repositoryClass: PointOfSaleRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['pos:read']],
    denormalizationContext: ['groups' => ['pos:write']]
)]
class PointOfSale
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 120)]
    #[Groups(['pos:read', 'pos:write'])]
    private string $name;

    #[ORM\Column(length: 255)]
    #[Groups(['pos:read', 'pos:write'])]
    private string $address = "";

    #[Groups(['pos:read', 'pos:write'])]
    #[ORM\Column(type: 'decimal', precision: 10, scale: 7)]
    private string $lat;

    #[Groups(['pos:read', 'pos:write'])]
    #[ORM\Column(type: 'decimal', precision: 10, scale: 7)]
    private string $lon;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
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

    public function getLat(): string
    {
        return $this->lat;
    }

    public function setLat(string $lat): self
    {
        $this->lat = $lat;
        return $this;
    }

    public function getLon(): string
    {
        return $this->lon;
    }

    public function setLon(string $lon): self
    {
        $this->lon = $lon;
        return $this;
    }
}

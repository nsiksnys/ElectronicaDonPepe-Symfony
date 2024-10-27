<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\SaleCommissionAmountRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SaleCommissionAmountRepository::class)]
#[ApiResource(
    operations: [ new Get(), new GetCollection(), new Post(), new Put() ],
    normalizationContext: ['groups' => ['sale_c_amount_read']],
    denormalizationContext: ['groups' => ['sale_c_amount_write']],
)]
class SaleCommissionAmount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['sale_c_amount_read'])]
    private ?int $id = null;

    #[ORM\Column(nullable: false)]
    #[Groups(['sale_c_amount_read', 'sale_c_amount_write'])]
    private ?int $min;

    #[ORM\Column(nullable: true)]
    #[Groups(['sale_c_amount_read', 'sale_c_amount_write'])]
    private ?int $max = null;

    #[ORM\Column]
    #[Groups(['sale_c_amount_read', 'sale_c_amount_write'])]
    private ?float $amount = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMin(): ?int
    {
        return $this->min;
    }

    public function setMin(int $min): static
    {
        $this->min = $min;

        return $this;
    }

    public function getMax(): ?int
    {
        return $this->max;
    }

    public function setMax(?int $max): static
    {
        $this->max = $max;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }
}

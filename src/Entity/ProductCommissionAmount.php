<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\ProductCommissionAmountRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductCommissionAmountRepository::class)]
#[ApiResource(
    operations: [ new Get(), new GetCollection(), new Post(), new Put() ],
    normalizationContext: ['groups' => ['product_c_amount_read']],
    denormalizationContext: ['groups' => ['product_c_amount_write']],
)]
class ProductCommissionAmount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product_c_amount_read'])]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'], inversedBy: 'commissionAmount')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['product_c_amount_read', 'product_c_amount_write'])]
    private ?Product $product = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['product_c_amount_read', 'product_c_amount_write'])]
    private ?float $amount = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }
}

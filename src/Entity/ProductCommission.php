<?php

namespace App\Entity;

use App\Repository\ProductCommissionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductCommissionRepository::class)]
class ProductCommission extends Commission
{
    #[ORM\ManyToOne]
    private ?Product $product = null;

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }
}

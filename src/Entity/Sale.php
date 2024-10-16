<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\SaleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: SaleRepository::class)]
#[ApiResource(
    operations: [ new Get(), new GetCollection(), new Post() ],
    normalizationContext: ['groups' => ['sale_read']],
    denormalizationContext: ['groups' => ['sale_write']],
)]
#[ApiFilter(DateFilter::class, properties: ['salesDate'])]
class Sale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['sale_read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['sale_read', 'sale_write'])]
    private ?\DateTimeInterface $salesDate = null;
 
    /**
     * @var Collection<int, Product>
     */
    #[ORM\ManyToMany(targetEntity: Product::class)]
    #[Groups(['sale_read', 'sale_write'])]
    private Collection $products;

    #[ORM\Column(nullable: true)]
    #[Groups(['sale_read', 'sale_write'])]
    private ?float $total = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['sale_read', 'sale_write'])]
    private ?Salesman $salesman = null;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->salesDate = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSalesDate(): ?\DateTimeInterface
    {
        return $this->salesDate;
    }

    public function setSalesDate(\DateTimeInterface $salesDate): static
    {
        $this->salesDate = $salesDate;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        $this->products->removeElement($product);

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(?float $total): static
    {
        if ($total == 0){
            foreach ($this->getProducts() as $product){
                $total += $product->getUnitPrice();
            }
        }
        $this->total = $total;

        return $this;
    }

    public function getSalesman(): ?Salesman
    {
        return $this->salesman;
    }

    public function setSalesman(?Salesman $salesman): static
    {
        $this->salesman = $salesman;

        return $this;
    }
}

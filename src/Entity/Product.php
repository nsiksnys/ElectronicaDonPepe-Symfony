<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    operations: [ new Get(), new GetCollection() ],
    normalizationContext: ['groups' => ['product_read']]
)]
#[ApiFilter(ExistsFilter::class, properties: ['campaigns', 'commissionAmount'])]
#[Groups('product_read')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['sale_read', 'campaign_read', 'product_c_amount_read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['sale_read', 'campaign_read', 'product_c_amount_read'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['sale_read', 'campaign_read', 'product_c_amount_read'])]
    private ?float $unitPrice = null;

    #[ORM\OneToMany(targetEntity: Campaign::class, mappedBy: 'product')]
    private Collection $campaigns;

    #[ORM\OneToOne(cascade: ['persist', 'remove'], mappedBy: 'product')]
    private ProductCommissionAmount $commissionAmount;

    public function __construct()
    {
        $this->campaigns = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getUnitPrice(): ?float
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(float $unitPrice): static
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    /**
     * @return Collection<int, Campaign>
     */
    public function getCampaigns(): Collection
    {
        return $this->campaigns;
    }

    public function getCommissionAmount(): ProductCommissionAmount
    {
        return $this->commissionAmount;
    }
}

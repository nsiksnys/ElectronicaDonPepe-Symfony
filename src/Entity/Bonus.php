<?php

namespace App\Entity;

use App\Repository\BonusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BonusRepository::class)]
class Bonus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFrom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateTo = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Salesman $salesman = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?SaleCommission $saleComission = null;

    /**
     * @var Collection<int, ProductCommission>
     */
    #[ORM\ManyToMany(targetEntity: ProductCommission::class, cascade: ['persist', 'remove'])]
    private Collection $ProductComissions;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Award $bestSalesmanMonth = null;

    /**
     * @var Collection<int, Award>
     */
    #[ORM\ManyToMany(targetEntity: Award::class, cascade: ['persist', 'remove'])]
    private Collection $campaigns;

    #[ORM\Column(nullable: true)]
    private ?float $productCommissionsTotal = null;

    #[ORM\Column(nullable: true)]
    private ?float $campaignAwardsTotal = null;

    #[ORM\Column(nullable: true)]
    private ?float $total = null;

    public function __construct()
    {
        $this->ProductComissions = new ArrayCollection();
        $this->campaigns = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getDateFrom(): ?\DateTimeInterface
    {
        return $this->dateFrom;
    }

    public function setDateFrom(\DateTimeInterface $dateFrom): static
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    public function getDateTo(): ?\DateTimeInterface
    {
        return $this->dateTo;
    }

    public function setDateTo(\DateTimeInterface $dateTo): static
    {
        $this->dateTo = $dateTo;

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

    public function getSaleComission(): ?SaleCommission
    {
        return $this->saleComission;
    }

    public function setSaleComission(?SaleCommission $saleComission): static
    {
        $this->saleComission = $saleComission;

        return $this;
    }

    /**
     * @return Collection<int, ProductCommission>
     */
    public function getProductComissions(): Collection
    {
        return $this->ProductComissions;
    }

    public function addProductComission(ProductCommission $productComission): static
    {
        if (!$this->ProductComissions->contains($productComission)) {
            $this->ProductComissions->add($productComission);
        }

        return $this;
    }

    public function removeProductComission(ProductCommission $productComission): static
    {
        $this->ProductComissions->removeElement($productComission);

        return $this;
    }

    public function getBestSalesmanMonth(): ?Award
    {
        return $this->bestSalesmanMonth;
    }

    public function setBestSalesmanMonth(?Award $bestSalesmanMonth): static
    {
        $this->bestSalesmanMonth = $bestSalesmanMonth;

        return $this;
    }

    /**
     * @return Collection<int, Award>
     */
    public function getCampaigns(): Collection
    {
        return $this->campaigns;
    }

    public function addCampaign(Award $campaign): static
    {
        if (!$this->campaigns->contains($campaign)) {
            $this->campaigns->add($campaign);
        }

        return $this;
    }

    public function removeCampaign(Award $campaign): static
    {
        $this->campaigns->removeElement($campaign);

        return $this;
    }

    public function getProductCommissionsTotal(): ?float
    {
        return $this->productCommissionsTotal;
    }

    public function setProductCommissionsTotal(?float $productCommissionsTotal): static
    {
        $this->productCommissionsTotal = $productCommissionsTotal;

        return $this;
    }

    public function getCampaignAwardsTotal(): ?float
    {
        return $this->campaignAwardsTotal;
    }

    public function setCampaignAwardsTotal(?float $campaignAwardsTotal): static
    {
        $this->campaignAwardsTotal = $campaignAwardsTotal;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(?float $total): static
    {
        $this->total = $total;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\SaleCommissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SaleCommissionRepository::class)]
class SaleCommission extends Commission
{
    /**
     * @var Collection<int, Sale>
     */
    #[ORM\ManyToMany(targetEntity: Sale::class)]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * @return Collection<int, Sale>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Sale $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
        }

        return $this;
    }

    public function removeItem(Sale $item): static
    {
        $this->items->removeElement($item);

        return $this;
    }
}

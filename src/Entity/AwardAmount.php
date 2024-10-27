<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use App\Repository\AwardAmountRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AwardAmountRepository::class)]
#[ApiResource(
    operations: [ new Get(), new GetCollection(), new Put() ],
    normalizationContext: ['groups' => ['award_amount_read']],
    denormalizationContext: ['groups' => ['award_amount_write']],
)]
#[ApiFilter(BooleanFilter::class, properties: ['campaign'])]
class AwardAmount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['award_amount_read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['award_amount_read', 'award_amount_write'])]
    private ?bool $campaign = null;

    #[ORM\Column]
    #[Groups(['award_amount_read', 'award_amount_write'])]
    private ?float $amount = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isCampaign(): ?bool
    {
        return $this->campaign;
    }

    public function setCampaign(bool $campaign): static
    {
        $this->campaign = $campaign;

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

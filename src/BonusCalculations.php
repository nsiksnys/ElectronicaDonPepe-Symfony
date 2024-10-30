<?php

namespace App;

use App\Entity\Award;
use App\Entity\AwardAmount;
use App\Entity\Bonus;
use App\Entity\Campaign;
use App\Entity\Product;
use App\Entity\ProductCommission;
use App\Entity\ProductCommissionAmount;
use App\Entity\Sale;
use App\Entity\SaleCommission;
use App\Entity\SaleCommissionAmount;
use App\Entity\Salesman;
use App\Repository\AwardRepository;
use App\Repository\AwardAmountRepository;
use App\Repository\BonusRepository;
use App\Repository\CampaignRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductCommissionRepository;
use App\Repository\ProductCommissionAmountRepository;
use App\Repository\SaleRepository;
use App\Repository\SaleCommissionRepository;
use App\Repository\SaleCommissionAmountRepository;
use App\Repository\SalesmanRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class BonusCalculations
{
    private EntityManagerInterface $entityManager;
    private AwardRepository $awardRepository;
    private AwardAmountRepository $awardAmountRepository;
    private BonusRepository $bonusRepository;
    private CampaignRepository $campaignRepository;
    private ProductRepository $productRepository;
    private ProductCommissionRepository $productCommissionRepository;
    private ProductCommissionAmountRepository $productCommissionAmountRepository;
    private SaleRepository $saleRepository;
    private SaleCommissionRepository $saleCommissionRepository;
    private SaleCommissionAmountRepository $saleCommissionAmountRepository;
    private SalesmanRepository $salesmanRepository;
    private \DateTimeInterface $today;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->awardRepository = $this->entityManager->getRepository(Award::class);
        $this->awardAmountRepository = $this->entityManager->getRepository(AwardAmount::class);
        $this->bonusRepository = $this->entityManager->getRepository(Bonus::class);
        $this->campaignRepository = $this->entityManager->getRepository(Campaign::class);
        $this->productRepository = $this->entityManager->getRepository(Product::class);
        $this->productCommissionRepository = $this->entityManager->getRepository(ProductCommission::class);
        $this->productCommissionAmountRepository = $this->entityManager->getRepository(ProductCommissionAmount::class);
        $this->saleRepository = $this->entityManager->getRepository(Sale::class);
        $this->saleCommissionRepository = $this->entityManager->getRepository(SaleCommission::class);
        $this->saleCommissionAmountRepository = $this->entityManager->getRepository(SaleCommissionAmount::class);
        $this->salesmanRepository = $this->entityManager->getRepository(Salesman::class);
    }

    public function calculateAll(string $from, array $salespeopleIds): array
    {
        $bonuses = [];
        $from = new DateTime($from);
        $this->today = new DateTime();
        $to = clone $from;
        $to->modify('last day of this month')->setTime(23,59,59);

        // Get the Salesman objects
        $salespeople = $this->salesmanRepository->findBy(['id' => $salespeopleIds]);

        // No sales found
        if ($this->saleRepository->countByDates($this->getStringDate($from), $this->getStringDate($to)) == 0){
            return [];
        }

        // Calculate awards
        $bestSalesmanAward = $this->calculateBestSalesmanAward($from, $to, $salespeople);
        $campaignAwards = $this->calculateAllCampaignAwards($from, $to, $salespeople);

        // Calcualte bonuses for each salesman
        foreach ($salespeople as $salesman) {
            $bonuses[] = $this->calculateBonus($from, $to, $salesman, $bestSalesmanAward, $campaignAwards);
        }
        
        return $bonuses;
    }

    public function calculateBonus(\DateTimeInterface $from, \DateTimeInterface $to, Salesman $salesman, Award $someonesBestSalesmanAward, array $campaignAwards): Bonus
    {
        $sales = $this->saleRepository->findBySalesmanAndDates($salesman, $this->getStringDate($from), $this->getStringDate($to));
        $productCommissions = $this->calculateProductCommission($salesman, $sales, $from, $to);
        $saleCommission = $this->calculateSaleCommission($salesman, $sales, $from, $to);
        $thisBestSalesman = null;
        $bonus = new Bonus();

        if (!empty($campaignAwards)) {
            foreach ($campaignAwards as $campaign) {
                if (($campaign != null) && ($salesman === $campaign->getAwardedTo()))
                    $bonus->addCampaign($campaign);
            }
        }

        foreach ($productCommissions as $productCommission) {
            $bonus->addProductComission($productCommission);
        }

        if (($someonesBestSalesmanAward != null) && ($someonesBestSalesmanAward->getAwardedTo() === $salesman))
            $thisBestSalesman = $someonesBestSalesmanAward;

        $bonus->setCreatedAt($this->today);
        $bonus->setDateFrom($from);
        $bonus->setDateTo($to);
        $bonus->setSalesman($salesman);
        $bonus->setSaleComission($saleCommission);
        $bonus->setBestSalesmanMonth($thisBestSalesman);
        $this->setTotals($bonus);

        $this->entityManager->persist($bonus);
        $this->entityManager->flush();

        return $bonus;
    }

    public function calculateProductCommission(Salesman $salesman, array $sales, \DateTimeInterface $from, \DateTimeInterface $to): ArrayCollection
    {
        $collection = new ArrayCollection();
        $commissionAmounts = $this->productCommissionAmountRepository->findAll();

        foreach ($sales as $sale) {
            foreach ($commissionAmounts as $commissionAmount) {
                //If the product in $commissionAmount is in this particulare sale
                $saleProducts = $sale->getProducts();
                if ($saleProducts->contains($commissionAmount->getProduct())) {
                    $units = $this->countProductsInSale($saleProducts, $commissionAmount->getProduct());

                    $productCommission = new ProductCommission();
                    $productCommission->setCreatedAt($this->today);
                    $productCommission->setFromDate($from);
                    $productCommission->setToDate($to);
                    $productCommission->setSalesman($salesman);
                    $productCommission->setUnits($units);
                    $productCommission->setTotal($commissionAmount->getAmount() * $units);
                    $productCommission->setProduct($commissionAmount->getProduct());

                    $collection->add($productCommission);
                }
            }
        }
        return $collection;
    }

    public function calculateSaleCommission(Salesman $salesman, array $sales, \DateTimeInterface $from, \DateTimeInterface $to): ?SaleCommission
    {
        $saleCommission = new SaleCommission();
        $commissionAmounts = $this->saleCommissionAmountRepository->findAll();

        if (empty($sales)) {
            return null;
        }

        $saleCommission->setCreatedAt($this->today);
        $saleCommission->setFromDate($from);
        $saleCommission->setToDate($to);
        $saleCommission->setSalesman($salesman);
        $saleCommission->setUnits(count($sales));

        foreach ($sales as $sale) {
            $saleCommission->addItem($sale);
        }

        foreach ($commissionAmounts as $commissionAmount) {
            if ($commissionAmount->getMax() != 0) //a zero max amount for an amount object means it doesn't have a max amount
            {
                if (count($sales) <= $commissionAmount->getMax() && count($sales) >= $commissionAmount->getMin())
                    $saleCommission->setTotal($commissionAmount->getAmount());
            } else {
                if (count($sales) >= $commissionAmount->getMin())
                    $saleCommission->setTotal($commissionAmount->getAmount());
            }
        }

        return $saleCommission;
    }

    public function calculateBestSalesmanAward(\DateTimeInterface $from, \DateTimeInterface $to, array $salespeople): ?Award
    {
        $count = 0;
        $award = new Award();
        $awardedTo = new Salesman();
        $awardAmount = $this->awardAmountRepository->findOneBy(['campaign' => false]);

        // Search for an existing award for this timeframe.
        // If there is a match, return it.
        $existingAwardSearch = $this->awardRepository->findBestSalesmanByDates($this->getStringDate($from), $this->getStringDate($to), $salespeople);
        if (count($existingAwardSearch) == 1) {
            return $existingAwardSearch[0];
        }

        foreach ($salespeople as $salesman) {
            $sales = $this->saleRepository->findBySalesmanAndDates($salesman, $this->getStringDate($from), $this->getStringDate($to));

            if (!empty($sales)) {
                if (count($sales) > $count) {
                    $count = count($sales);
                    $awardedTo = $salesman;
                }
            }
        }

        $award->setCreatedAt(new DateTime());
        $award->setDateFrom($from);
        $award->setDateTo($to);
        $award->setAwardedTo($awardedTo);
        $award->setCampaign(false);
        $award->setTotal($awardAmount->getAmount());

        if ($count == 0)
            return null;

        return $award;
    }

    public function calculateAllCampaignAwards(\DateTimeInterface $from, \DateTimeInterface $to, array $salespeople) : array
    {
        $campaigns = $this->campaignRepository->findBy(['active' => true]);
        $awards = [];
        
        foreach ($campaigns as $campaign){
            $awards[] = $this->calculateCampaignAward($from, $to, $salespeople, $campaign->getProduct());
        }

        return $awards;
    }

    public function calculateCampaignAward(\DateTimeInterface $from, \DateTimeInterface $to, array $salespeople, Product $product): ?Award
    {
        $awardAmount = $this->awardAmountRepository->findOneBy(['campaign' => true]);
        $award = null;
        $productMaxCount = 0;

        // Search for an existing award for this timeframe.
        // If there is a match, return it.
        $existingAwardSearch = $this->awardRepository->findCampaignAwardByDatesAndProduct($this->getStringDate($from), $this->getStringDate($to), $salespeople, $product);
        if (count($existingAwardSearch) == 1) {
            return $existingAwardSearch[0];
        }

        foreach ($salespeople as $salesman) {
            $sales = $this->saleRepository->findBySalesmanAndDates($salesman, $this->getStringDate($from), $this->getStringDate($to));

            if (!empty($sales)) {
                foreach ($sales as $sale) {
                    $productInSaleCount = $this->countProductsInSale($sale->getProducts(), $product);

                    if ($productInSaleCount > $productMaxCount) {
                        $productMaxCount = $productInSaleCount;
                        $awardedSalesman = $salesman;
                    }
                }
            }
        }

        if ($productMaxCount > 0) {
            $award = new Award();
            $award->setCreatedAt(new DateTime());
            $award->setDateFrom($from);
            $award->setDateTo($to);
            $award->setAwardedTo($awardedSalesman);
            $award->setCampaign(true);
            $award->setProduct($product);
            $award->setTotal($awardAmount->getAmount());
            $awards[] = $award;
        }

        return $award;
    }
    
    public function countProductsInSale(array|Collection $saleProducts, Product $product): int
    {
        $count = 0;
        foreach ($saleProducts as $item)
            if ($item === $product) {
                $count++;
            }
        return $count;
    }

    public function searchProductInSale(array $saleProducts, Product  $product): bool
    {
        foreach ($saleProducts as $item)
            if ($item === $product) {
                return true;
            }
        return false;
    }
    
    public function getStringDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function setTotals(Bonus &$bonus)
    {
        $subtotals = $this->calculateSubtotals($bonus);
        $bonus->setProductCommissionsTotal($subtotals['productCommissions']);
        $bonus->setCampaignAwardsTotal($subtotals['campaignAwards']);
        $bonus->setTotal($subtotals['total']);
    }

    public function calculateSubtotals(Bonus $bonus): array
    {
        $subtotals = [
            'productCommissions' => 0,
            'campaignAwards' => 0,
            'total' => 0
        ];

        //product commission
        if (!empty($bonus->getProductComissions())) {
            foreach ($bonus->getProductComissions() as $productCommission)
                $subtotals['productCommissions'] += $productCommission->getTotal();
        }

        //product campaign award
        if (!empty($bonus->getCampaigns())) {
            foreach ($bonus->getCampaigns() as $campaign)
                $subtotals['campaignAwards'] += $campaign->getTotal();
        }

        //Bonus total
        $subtotals['total'] = $subtotals['productCommissions'] + $subtotals['campaignAwards'];

        if ($bonus->getSaleComission() != null)
            $subtotals['total'] += $bonus->getSaleComission()->getTotal();

        if ($bonus->getBestSalesmanMonth() != null)
            $subtotals['total'] += $bonus->getBestSalesmanMonth()->getTotal();

        return $subtotals;
    }

    public function updateAllExistingBonus(float $newAmountValue, float $oldAmountValue, string $type, bool $isCampaign = null) : int
    {
        $updatedCounter = 0;
        
        switch($type)
        {
            case "product":
                $updatedCounter = $this->updatedExistingProductCommission($newAmountValue, $oldAmountValue);
                break;
            case "sale":
                $updatedCounter = $this->updateExistingSaleCommission($newAmountValue, $oldAmountValue);
                break;
            case "award":
                $updatedCounter = $this->updateExistingAwards($newAmountValue, $oldAmountValue, $isCampaign);
                break;
        }

        $bonuses = $this->bonusRepository->findAll();

        foreach($bonuses as $bonus)
        {
            $oldBonus = $bonus;
            $this->setTotals($bonus);
            if ($bonus->getTotal() != $oldBonus->getTotal()){
                $this->entityManager->flush();
                $updatedCounter++;
            }
        }
        return $updatedCounter;
    }


    public function updateExistingAwards(float $newAmountValue, float $oldAmountValue, bool $isCampaign): int
    {
        $existingAwards = $this->awardRepository->findBy(['campaign' => $isCampaign]);
        $updatedCounter = 0;

        foreach ($existingAwards as $award)
        {
            if ($award->getTotal() == $oldAmountValue) {
                $award->setTotal($newAmountValue);
                $this->entityManager->flush();
                $updatedCounter++;
            }
        }

        return $updatedCounter;
    }

    public function updatedExistingProductCommission(float $newAmountValue, float $oldAmountValue): int
    {
        $existingProductCommissions = $this->productCommissionRepository->findAll();
        $updatedCounter = 0;

        foreach ($existingProductCommissions as $commission)
        {
            if (($commission->getTotal() / $commission->getUnits()) == $oldAmountValue) {
                $commission->setTotal($commission->getUnits() * $newAmountValue);
                $this->entityManager->flush();
                $updatedCounter++;
            }
        }

        return $updatedCounter;
    }

    public function updateExistingSaleCommission(float $newAmountValue, float $oldAmountValue): int
    {
        $existingSaleCommissions = $this->saleCommissionRepository->findAll();
        $updatedCounter = 0;

        foreach ($existingSaleCommissions as $commission)
        {
            if ($commission->getTotal() == $oldAmountValue) {
                $commission->setTotal($newAmountValue);
                $this->entityManager->flush();
                $updatedCounter++;
            }
        }

        return $updatedCounter;
    }
}

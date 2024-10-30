<?php

namespace App\Controller;

use ApiPlatform\Metadata\Exception\RuntimeException;
use App\BonusCalculations;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Psr\Log\LoggerInterface;

#[AsController]
class BonusController extends AbstractController
{
    private $bonusCalculations;
    private $logger;

    public function __construct(BonusCalculations $bonusCalculations, LoggerInterface $logger)
    {
        $this->bonusCalculations = $bonusCalculations;
        $this->logger = $logger;
    }
    
    public function __invoke(Request $request)
    {
        $fromDate = $request->get('date','');
        $salespeople = $request->get('salespeople',[]);

        $this->logger->info("Calculate bonuses from $fromDate for salesmen " . implode(',',$salespeople));
        
        try{
            return $this->bonusCalculations->calculateAll($fromDate, $salespeople);
        }
        catch(\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new RuntimeException("Something went wrong. Please try again.");
        }
    }
}
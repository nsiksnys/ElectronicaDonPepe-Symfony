<?php

namespace App\Controller;

use App\Entity\Campaign;
use App\Repository\CampaignRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/campaign')]
class CampaignController extends AbstractController
{
    #[Route('/', name: 'app_campaign_index', methods: ['GET', 'POST'])]
    public function index(CampaignRepository $campaignRepository, ProductRepository $productRepository): Response
    {
        $campaigns = $campaignRepository->findAll();
        $availableProducts = $productRepository->findProductsInNoCampaign($campaignRepository->getSelectDistinctQuery());

        return $this->render("campaign/index.html.twig", [
            'campaigns' => $campaigns,
            'products' => $availableProducts
        ]);
    }

    #[Route('/disable/{id}', name: 'app_campaign_disable', methods: ['GET'])]
    public function disable(string $id, EntityManagerInterface $entityManager, CampaignRepository $campaignRepository): Response
    {
        $campaign = $campaignRepository->findOneBy(['id' => $id]);
        $campaign->setActive(false);

        $entityManager->flush();

        return $this->redirectToRoute('app_campaign_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/enable/{id}', name: 'app_campaign_enable', methods: ['GET'])]
    public function enable(string $id, EntityManagerInterface $entityManager, CampaignRepository $campaignRepository): Response
    {
        $campaign = $campaignRepository->findOneBy(['id' => $id]);
        $campaign->setActive(true);

        $entityManager->flush();

        return $this->redirectToRoute('app_campaign_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/add/{productId}', name: 'app_campaign_add', methods: ['GET'])]
    public function add(string $productId, EntityManagerInterface $entityManager, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneBy([ 'id' => $productId]);
        
        $newCampaign = new Campaign();
        $newCampaign->setCreatedAt(new \DateTime());
        $newCampaign->setProduct($product);
        $newCampaign->setActive(true);

        $entityManager->persist($newCampaign);
        $entityManager->flush();

        return $this->redirectToRoute('app_campaign_index', [], Response::HTTP_SEE_OTHER);
    }
}
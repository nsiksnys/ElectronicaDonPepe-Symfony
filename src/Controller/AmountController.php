<?php

namespace App\Controller;

use App\Entity\Bonus;
use App\Entity\ProductCommissionAmount;
use App\Repository\AwardAmountRepository;
use App\Repository\ProductCommissionAmountRepository;
use App\Repository\ProductRepository;
use App\Repository\SaleCommissionAmountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\BonusCalculations;
use App\Entity\AwardAmount;

#[Route('/amount')]
class AmountController extends AbstractController
{
    #[Route('/', name: 'app_amount_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager, ProductRepository $productRepository, AwardAmountRepository $awardAmountRepository, ProductCommissionAmountRepository $productCommissionAmountRepository, SaleCommissionAmountRepository $saleCommissionAmountRepository): Response
    {
        $availableProducts = $productRepository->findProductsInNoCommission($productCommissionAmountRepository->getSelectDistinctQuery());
        $saleCommissions = $saleCommissionAmountRepository->findAll();
        $productCommissions = $productCommissionAmountRepository->findAll();
        $awards = $awardAmountRepository->findAll();

        return $this->render('amount/index.html.twig', [
            'products' => $availableProducts,
            'saleCommissions' => $saleCommissions,
            'productCommissions' => $productCommissions,
            'awards' => $awards
        ]);
    }

    #[Route('/add/product/{id}', name: 'app_amount_add_product', methods: ['GET', 'POST'])]
    public function addProduct(String $id, Request $request, EntityManagerInterface $entityManager, ProductRepository $productRepository): JsonResponse|Response
    {
        /* @var App\Enity\Product $product */
        $product = $productRepository->find($id);
        $form = $this->createFormBuilder()
                ->setAction($this->generateUrl('app_amount_add_product', ['id' => $id]))
                ->add('product', TextType::class, [
                    'label' => "Producto",
                    'data' => $product->getName(),
                    'disabled' => true,
                    'label_attr' => [ 'class' => 'form-label'],
                    'attr' => [ 'class' => 'form-control']
                ])
                ->add('amount', TextType::class, [
                    'label' => "Monto",
                    'label_attr' => [ 'class' => 'form-label'],
                    'attr' => [ 'class' => 'form-control']
                ])
                ->add('save', SubmitType::class, [
                    'label' => 'Crear',
                    'attr' => ['class' => 'btn btn-primary']
                ])
                ->getForm();
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $newProductCommissionAmount = new ProductCommissionAmount();
            $newProductCommissionAmount->setProduct($product);
            $newProductCommissionAmount->setAmount($formData['amount']);
            
            // TODO: validate the amount value is not garbage

            //Save changes
            $entityManager->persist($newProductCommissionAmount);
            $entityManager->flush();
            return $this->redirectToRoute('app_amount_index', [], Response::HTTP_SEE_OTHER);
        }

        // Return rendered form as json
        return $this->json(['form' => $this->render('amount/_form.html.twig', [
            'form' => $form
        ])->getContent()]);
    }

    #[Route('/edit/{type}/{id}', name: 'app_amount_edit', methods: ['GET', 'POST'])]
    public function edit(String $type, String $id, Request $request, EntityManagerInterface $entityManager, AwardAmountRepository $awardAmountRepository, ProductCommissionAmountRepository $productCommissionAmountRepository, SaleCommissionAmountRepository $saleCommissionAmountRepository, BonusCalculations $bonusCalculations): JsonResponse|Response
    {
        $form = $this->createFormBuilder()
                ->setAction($this->generateUrl('app_amount_edit', ['type' => $type, 'id' => $id]));

        switch($type)
        {
            case "product":
                /* @var App\Enity\ProductCommissionAmount $amount */
                $amount = $productCommissionAmountRepository->findOneBy(['id' => $id]);
                $form = $form->add('product', TextType::class, [
                    'label' => "Producto",
                    'data' => $amount->getProduct()->getName(),
                    'disabled' => true,
                    'label_attr' => [ 'class' => 'form-label'],
                    'attr' => [ 'class' => 'form-control']
                ]);
                break;
            case "sale":
                /* @var App\Enity\SaleCommissionAmount $amount */
                $amount = $saleCommissionAmountRepository->findOneBy(['id' => $id]);
                break;
            case "award":
                /* @var App\Enity\AwardCommissionAmount $amount */
                $amount = $awardAmountRepository->findOneBy(['id' => $id]);
                if ($amount->isCampaign())
                {
                    $awardType = "Campaña";
                }
                else 
                {
                    $awardType = "Mejor vendedor mes";
                }
                $form = $form->add('type', TextType::class, [
                    'label' => "Tipo",
                    'data' => $awardType,
                    'disabled' => true,
                    'label_attr' => [ 'class' => 'form-label'],
                    'attr' => [ 'class' => 'form-control']
                ]);
            break;
        }

        $form = $form->add('amount', TextType::class, [
                    'label' => "Monto",
                    'data' => $amount->getAmount(),
                    'label_attr' => [ 'class' => 'form-label'],
                    'attr' => [ 'class' => 'form-control']
                ])
                ->add('save', SubmitType::class, [
                    'label' => 'Actualizar',
                    'attr' => ['class' => 'btn btn-primary']
                ])
                ->getForm();
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $oldAmount = $amount->getValue();
            $newAmount = $formData['amount'];

            // TODO: validate the amount value is not garbage
            //Save changes
            $amount->setAmount($formData['amount']);
            $entityManager->persist($amount);
            $entityManager->flush();

            // TODO: What happens if we change the amounts but there are bonuses already created?
            //       How do we recalculate the bonuses?
            //$bonusCalculations->updateAllExistingBonus($newAmount, $oldAmount, $type, ($amount instanceof AwardAmount)? $amount->isCampaign() : null);

            return $this->redirectToRoute('app_amount_index', [], Response::HTTP_SEE_OTHER);
        }

        // Return rendered form as json
        return $this->json(['form' => $this->render('amount/_form.html.twig', [
            'form' => $form
        ])->getContent()]);
    }
}

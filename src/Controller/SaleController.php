<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Sale;
use App\Entity\Salesman;
use App\Repository\SaleRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sale')]
class SaleController extends AbstractController
{

    #[Route('/', name: 'app_sale_index', methods: ['GET'])]
    public function index(Request $request, SaleRepository $saleRepository): JsonResponse
    {
        $sales = [];
        $from = $request->query->get('from', "");
        $to = $request->query->get('to', "");

        // If there is no search by date, return all sale records
        if ((!isset($from) || $from == "") && (!isset($to) || $to == "")) {
            $sales = $saleRepository->findAll();
        }
        else {
            $sales = $saleRepository->findByDates($from, $to);
        }
        
        return $this->json($sales);
    }
/* 
    #[Route('/new', name: 'app_sale_new', methods: ['POST'])]
    public function newGet(Request $request, EntityManagerInterface $entityManager, ProductRepository $productRepository): Response
    {
        $sale = new Sale();        
        //$form = $this->createForm(SaleType::class, $sale);
        $form = $this->createFormBuilder($sale)
                     ->add('salesDate', DateTimeType::class, [
                        'label' => 'Fecha de venta',
                        'label_attr' => [ 'class' => 'form-label'],
                        'attr' => [ 'class' => 'form-control']
                     ])
                     ->add('products', EntityType::class, [
                        'class' => Product::class,
                        'choice_label' => function ($product){
                            return $product->getName()." ($".$product->getUnitPrice()." p.u.)";
                        },
                        'multiple' => true,
                        'label' => 'Productos',
                        'label_attr' => [ 'class' => 'form-label'],
                        'attr' => [ 'class' => 'form-select']                                        
                    ])
                     ->add('salesman', EntityType::class, [
                        'class' => Salesman::class,
                        'choice_label' => function ($salesman) {
                            return $salesman->getFullName();
                           },
                        'label' => 'Vendedor',
                        'label_attr' => [ 'class' => 'form-label'],
                        'attr' => [ 'class' => 'form-select']                                    
                    ])
                    ->add('save', SubmitType::class, [
                        'label' => 'Guardar', 
                        'attr' => [ 'class' => 'btn btn-primary']
                    ])
                    ->getForm();
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sale = $form->getData();
            $sale->setTotal($this->calculateTotal($sale));
            $entityManager->persist($sale);
            $entityManager->flush();

            return $this->redirectToRoute('app_sale_index', [], Response::HTTP_SEE_OTHER);
        }
 
        return $this->render('sale/new.html.twig', [
            'sale' => $sale,
            'form' => $form
        ]);
    }
*/
    #[Route('/{id}', name: 'app_sale_show', methods: ['GET'])]
    public function show(Sale $sale): JsonResponse
    {
        return $this->json($sale);
    }

    private function calculateTotal(Sale $sale): float
    {
        $total = 0;
        foreach ($sale->getProducts() as $item){
            $total += $item->getUnitPrice();
        }
        return $total;
    }
}

<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Sale;
use App\Entity\Salesman;
use App\Repository\SaleRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sale')]
class SaleController extends AbstractController
{
    #[Route('/', name: 'app_sale_index', methods: ['GET'])]
    public function index(SaleRepository $saleRepository): Response
    {
        return $this->render('sale/index.html.twig', [
            'sales' => $saleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_sale_new', methods: ['GET', 'POST'])]
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
                        'attr' => [ 'class' => 'btn-primary']
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

    #[Route('/{id}', name: 'app_sale_show', methods: ['GET'])]
    public function show(Sale $sale): Response
    {
        return $this->render('sale/show.html.twig', [
            'sale' => $sale,
        ]);
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

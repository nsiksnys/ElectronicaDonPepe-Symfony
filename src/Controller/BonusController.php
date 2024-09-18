<?php

namespace App\Controller;

use App\BonusCalculations;
use App\Entity\Award;
use App\Entity\Bonus;
use App\Entity\Campaign;
use App\Entity\Sale;
use App\Entity\Salesman;
use App\Repository\BonusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/bonus')]
class BonusController extends AbstractController
{
    #[Route('/', name: 'app_bonus_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, BonusRepository $bonusRepository, BonusCalculations $bonusCalculations): Response
    {
        $bonuses = $bonusRepository->findAll();
        $form = $this->createFormBuilder()
                     ->add('date', DateType::class, [
                        'label' => 'Fecha',
                        'widget' => 'single_text',
                        'input' => 'string',
                        'label_attr' => [ 'class' => 'form-label'],
                        'attr' => [ 'class' => 'form-control']
                     ])
                     ->add('salespeople', EntityType::class, [
                        'class' => Salesman::class,
                        'choice_label' => function ($salesman) {
                            return $salesman->getFullName();
                           },
                        'label' => 'Vendedor(es)',
                        'label_attr' => [ 'class' => 'form-label'],
                        'attr' => [ 'class' => 'form-select'],
                        'multiple' => true
                    ])
                    ->add('submit', SubmitType::class, [
                        'label' => 'Buscar', 
                        'attr' => [ 'class' => 'btn btn-primary']
                    ])
                    ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            try{
                $bonuses = $bonusCalculations->calculateAll($formData['date'], $formData['salespeople']->toArray());
            }
            catch (\Exception $exception){
                return $this->render("bonus/index.html.twig", [
                    'errors' => $exception->getMessage(),
                    'form' => $form,
                    'bonuses' => $bonuses
                ]);
            }
        }

        return $this->render("bonus/index.html.twig", [
            'form' => $form,
            'bonuses' => $bonuses
        ]);
    }
}
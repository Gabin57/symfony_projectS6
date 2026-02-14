<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\Product\ProductTypeStepOneType;
use App\Form\Product\ProductTypeStepTwoType;
use App\Form\Product\ProductTypeStepThreePhysicalType;
use App\Form\Product\ProductTypeStepThreeDigitalType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/product')]
#[IsGranted('ROLE_ADMIN')]
class ProductFlowController extends AbstractController
{
    #[Route('/new', name: 'admin_product_new_start')]
    public function startNew(Request $request): Response
    {
        $request->getSession()->remove('product_flow_data');
        $request->getSession()->remove('product_flow_id');
        return $this->redirectToRoute('admin_product_step', ['step' => 1]);
    }

    #[Route('/edit/{id}', name: 'admin_product_edit_start')]
    public function startEdit(Product $product, Request $request): Response
    {
        $request->getSession()->set('product_flow_id', $product->getId());
        // Pre-fill session with existing data if needed, or just load entity in steps
        // For simplicity in this flow, we might want to edit directly or re-use the flow.
        // Let's re-use the flow but pre-load data.
        // Actually, complex multi-step editing is tricky. 
        // Strategy: Load entity, put its data into session? Or just work on the entity?
        // Let's work on the entity ID stored in session.
        return $this->redirectToRoute('admin_product_step', ['step' => 1]);
    }

    #[Route('/step/{step}', name: 'admin_product_step')]
    public function step(int $step, Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $productId = $session->get('product_flow_id');
        
        if ($productId) {
            $product = $entityManager->getRepository(Product::class)->find($productId);
        } else {
            $product = new Product();
        }

        // Handle Form Logic
        $form = null;
        $template = 'product/flow/step' . $step . '.html.twig';
        $nextStep = $step + 1;
        $prevStep = $step - 1;

        switch ($step) {
            case 1:
                $form = $this->createForm(ProductTypeStepOneType::class, $product);
                break;
            case 2:
                $form = $this->createForm(ProductTypeStepTwoType::class, $product);
                break;
            case 3:
                if ($product->getType() === 'physical') {
                    $form = $this->createForm(ProductTypeStepThreePhysicalType::class, $product);
                } elseif ($product->getType() === 'digital') {
                    $form = $this->createForm(ProductTypeStepThreeDigitalType::class, $product);
                } else {
                    // Logic error or type not set, go back
                    return $this->redirectToRoute('admin_product_step', ['step' => 1]);
                }
                break;
            case 4:
                // confirmation / summary
                // If price > 100, show extra confirmation?
                // Let's just show summary.
                if ($request->isMethod('POST')) {
                   // Save everything
                   if (!$product->getId()) {
                       $entityManager->persist($product);
                   }
                   $entityManager->flush();
                   $session->remove('product_flow_id');
                   return $this->redirectToRoute('app_product_index');
                }

                return $this->render('product/flow/summary.html.twig', [
                    'product' => $product,
                    'step' => $step
                ]);
            default:
                return $this->redirectToRoute('app_product_index');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Save intermediate state (or just keep object in memory if not persisting yet?)
            // To persist between requests without saving to DB, we strictly need to serialize to session 
            // OR save to DB as "draft".
            // "The session method"
            // But we are working with an Entity.
            // Let's save to DB immediately as a draft? Or just keep in session?
            // "Les données saisies sont conservées entre les étapes."
            // Easiest: Persist to DB, maybe with a 'status' = 'draft'.
            // For now, let's persist.
            
            $entityManager->persist($product);
            $entityManager->flush();
            $session->set('product_flow_id', $product->getId());

            return $this->redirectToRoute('admin_product_step', ['step' => $nextStep]);
        }

        return $this->render($template, [
            'form' => $form->createView(),
            'step' => $step,
            'product' => $product // for context
        ]);
    }
}

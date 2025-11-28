<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductFormType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur pour la gestion des produits dans le back-office.
 *
 * @package App\Controller\Admin
 */
#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/product')]
final class AdminProductController extends AbstractController
{
    /**
     * Crée un nouveau produit.
     *
     * @param Request $request Requête HTTP.
     * @param ProductRepository $productRepository Repository des produits.
     *
     * @return Response Réponse HTTP après création du produit.
     */
    #[Route('/new', name: 'app_admin_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);

            $this->addFlash('success', 'Le produit a bien été créé.');
        } else {
            $this->addFlash('error', 'Le formulaire contient des erreurs.');
        }
            return $this->redirectToRoute('app_admin');
        }

    /**
     * Modifie un produit existant.
     *
     * @param Request $request Requête HTTP.
     * @param Product $product Produit à modifier.
     * @param ProductRepository $productRepository Repository des produits.
     *
     * @return Response Réponse HTTP après modification du produit.
     */
    #[Route('/{id}/edit', name: 'app_admin_product_edit', methods: ['POST'])]
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);
            $this->addFlash('success', 'Le produit a bien été modifié.');
        } else {
            $this->addFlash('error', 'Le formulaire de modification contient des erreurs.');
        }

        return $this->redirectToRoute('app_admin');
    }

    /**
     * Supprime un produit.
     *
     * @param Request $request Requête HTTP.
     * @param Product $product Produit à supprimer.
     * @param ProductRepository $productRepository Repository des produits.
     *
     * @return Response Réponse HTTP après suppression du produit.
     */
    #[Route('/{id}', name: 'app_admin_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
            $this->addFlash('success', 'Le produit a bien été supprimé.');
        }

        return $this->redirectToRoute('app_admin');
    }
}

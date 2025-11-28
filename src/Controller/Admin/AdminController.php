<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductFormType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur pour les pages d'administration.
 *
 * Accessible uniquement aux utilisateurs avec le rôle ROLE_ADMIN.
 */
#[IsGranted('ROLE_ADMIN')]
#[Route('/admin')]
final class AdminController extends AbstractController
{
    /**
     * Page d'accueil du back-office : liste des produits.
     *
     * Route : /admin/
     *
     * @param ProductRepository $productRepository Repository des produits.
     *
     * @return Response Page HTML du back-office.
     */
    #[Route('/', name: 'app_admin', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        $newProduct = new Product();
        $creationForm = $this->createForm(ProductFormType::class, $newProduct, [
            'action' => $this->generateUrl('app_admin_product_new'),
            'method' => 'POST',
        ]);

        $editForms = [];
        foreach ($products as $product) {
            $editForms[$product->getId()] = $this->createForm(ProductFormType::class, $product, [
                'action' => $this->generateUrl('app_admin_product_edit', ['id' => $product->getId()]),
                'method' => 'POST',
            ])->createView();
        }

        return $this->render('admin/index.html.twig', [
            'products' => $products,
            'creationForm' => $creationForm->createView(),
            'editForms' => $editForms,
        ]);
    }
}
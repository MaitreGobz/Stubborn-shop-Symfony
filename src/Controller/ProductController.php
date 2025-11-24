<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur des produits (front-office).
 * Gère la liste /products et la fiche /product/{id}.
 */
final class ProductController extends AbstractController
{
    /**
     * Page listant tous les sweats avec filtre par prix.
     *
     * Route : /products
     * Filtre possible via query param "range" :
     * - 10-29
     * - 29-35
     * - 35-50
     *
     * @param Request $request Requête HTTP courante.
     * @param ProductRepository $productRepository Repo des produits.
     * @return Response Page HTML rendue.
     */
    #[Route('/products', name: 'app_products', methods: ['GET'])]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        // Retrieving the filter parameter from the URL
        $range = $request->query->get('range');

        // Call the Repository to obtain the filtered products
        $products = $productRepository->findByPriceRange($range);

        // Returning the Twig view with the useful data
        return $this->render('product/index.html.twig', [
            'products' => $products,
            'currentRange' => $range,
        ]);
    }
}

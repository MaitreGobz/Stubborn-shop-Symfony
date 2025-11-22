<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;

final class HomeController extends AbstractController
{
    /**
     * Page d'accueil
     * 
     * - Affiche la liste des produits en avant
     * - Adaptation des liens du menu et des boutons "Voir" si l'utilisateur est connecté
     * 
     * @param ProductRepository $productRepository Accès aux produits en base de donnée.
     * 
     * @return Response Page HTML de la home.
     */
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository): Response
    {
        $featuredProducts =$productRepository->findFeatured(3);

        return $this->render('home/index.html.twig', [
            'featuredProducts' => $featuredProducts,
        ]);
    }
}

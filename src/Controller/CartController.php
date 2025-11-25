<?php

namespace App\Controller;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur du panier d'achat (front-office).
 * Gère l'affichage et la validation du panier.
 */
final class CartController extends AbstractController
{
    private CartService $cartService;

    /**
     * Constructeur du contrôleur panier.
     */
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Page du panier d'achat
     * 
     * - Affiche la liste des produits dans le panier
     * - Calcul le prix du panier
     * - Validation de la commande
     * 
     * Route : /cart
     */
    #[Route('/cart', name: 'app_cart', methods: ['GET'])]
    public function index(): Response
    {
        $cartItems = $this->cartService->getItems();
        $total = $this->cartService->getTotal();
        
        return $this->render('cart/index.html.twig', [
            'items' => $cartItems,
            'total' => $total,
        ]);
    }

    /**
     * Retire un produit du panier.
     * 
     * Route : /cart/remove/{id}/{size}
     * 
     * @param int $id ID du produit à retirer.
     * @param string $size Taille du produit à retirer.
     * 
     * @return Response Redirection vers la page du panier.
     */
    #[Route('cart/remove/{id}/{size}', name: 'app_cart_remove', methods: ['POST'])]
    public function removeItem(int $id, string $size): Response
    {
        $this->cartService->removeItem($id, $size);

        $this->addFlash('success', 'Produit retiré du panier');

        return $this->redirectToRoute('app_cart');
    }
}

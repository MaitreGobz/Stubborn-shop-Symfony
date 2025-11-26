<?php

namespace App\Controller;

use App\Service\CartService;
use App\Service\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Contrôleur du panier d'achat (front-office).
 * Gère l'affichage et la validation du panier.
 */
final class CartController extends AbstractController
{
    /**
     * Service de gestion du panier.
     *
     * @var CartService
     */
    private CartService $cartService;

    /**
     * Service de paiement Stripe.
     *
     * @var StripeService
     */
    private StripeService $stripeService;

    /**
     * Constructeur du contrôleur panier.
     * 
     * @param CartService $cartService Service de gestion du panier.
     * @param StripeService $stripeService Service de paiement Stripe.
     */
    public function __construct(CartService $cartService, StripeService $stripeService)
    {
        $this->cartService = $cartService;
        $this->stripeService = $stripeService;
    }

    /**
     * Page du panier d'achat
     * 
     * - Affiche la liste des produits dans le panier
     * - Calcul le prix du panier
     * - Validation de la commande
     * 
     * Route : /cart
     * 
     * @return Response La réponse HTTP contenant la vue du panier.
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
    #[Route('/cart/remove/{id}/{size}', name: 'app_cart_remove', methods: ['POST'])]
    public function removeItem(int $id, string $size): Response
    {
        $this->cartService->removeItem($id, $size);

        $this->addFlash('success', 'Produit retiré du panier');

        return $this->redirectToRoute('app_cart');
    }

    /**
     * Valide le panier et crée une session de paiement Stripe.
     * 
     * Route : /cart/checkout
     * 
     * @return RedirectResponse Redirection vers la page de paiement Stripe.
     */
    #[Route('/cart/checkout', name: 'app_cart_checkout', methods: ['POST'])]
    public function checkout(): RedirectResponse
    {
        $cartItems = $this->cartService->getItems();

        // Preparing articles for Stripe
        $stripeItems = [];
        foreach ($cartItems as $item) {
            $product = $item['product'];
            $quantity = $item['quantity'];

            $stripeItems[] = [
                'name' => $product->getName(),
                'amount' => (int) round($product->getPrice() * 100),
                'quantity' => $quantity,
            ];
        }

        // Creating success and cancellation URLs
        $successUrl = $this->generateUrl('payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $cancelUrl = $this->generateUrl('payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL);

        // Creating the Stripe payment session
        $session = $this->stripeService->createCheckoutSession(
            $stripeItems,
            $successUrl,
            $cancelUrl
        );

        // Redirecting to the Stripe payment page
        return new RedirectResponse($session->url);
    }
}

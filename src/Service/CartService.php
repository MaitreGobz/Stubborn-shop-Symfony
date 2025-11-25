<?php

namespace App\Service;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Service de gestion du panier en session.
 */
class CartService
{
    private SessionInterface $session;

    /**
     * Accès à la session utilisateur
     * 
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    /**
     * Ajoute un produit au panier.
     * 
     * @param Product $product
     * @param string $size
     * 
     * @return void
     */
    public function add(Product $product, string $size): void
    {
        $cart = $this->session->get('cart',[]);
        $key = $product->getId(). '_' .$size;

        if (!isset($cart[$key])) {
            $cart[$key] = [
                'productId' => $product->getId(),
                'size' => $size,
                'quantity' => 0
            ];
        }

        $cart[$key]['quantity']++;

        $this->session->set('cart', $cart);
    }
}
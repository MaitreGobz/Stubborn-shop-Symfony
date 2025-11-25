<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Service de gestion du panier en session.
 */
class CartService
{
    /**
     * Clé de stockage du panier dans la session.
     * 
     * @var string
     */
    private const CART_KEY = 'cart';

    /**
     * Session utilisateur.
     * 
     * @var SessionInterface
     */
    private SessionInterface $session;

    /**
     * Repository des produits.
     * 
     * @var ProductRepository $productRepository
     */
    private ProductRepository $productRepository;

    /**
     * Constructeur du service panier.
     * 
     * @param RequestStack $requestStack Accès aà la session utilisateur.
     * @param ProductRepository $productRepository Accès aux produits en base de donnée.
     */
    public function __construct(RequestStack $requestStack, ProductRepository $productRepository)
    {
        $this->session = $requestStack->getSession();
        $this->productRepository = $productRepository;
    }

    /**
     * Ajoute un produit au panier, si la ligne existe déjà la quantité est incrémentée.
     * 
     * @param Product $product Produit à ajouter.
     * @param string $size Taille du produit.
     * 
     * @return void
     */
    public function addToCart(Product $product, string $size): void
    {
        $cart = $this->getCart();
        $key = $product->getId() . '_' . $size;

        if (!isset($cart[$key])) {
            $cart[$key] = [
                'productId' => $product->getId(),
                'size'      => $size,
                'quantity'  => 0,
            ];
        }

        $cart[$key]['quantity']++;

        $this->saveCart($cart);
    }

    /**
     * Supprime une ligne du panier pour un produit et une taille donnés.
     * 
     * @param int $productId Identifiant d'un produit.
     * @param string $size Taille du dit produit.
     * 
     * @return void
     */
    public function removeItem(int $productId, string $size): void
    {
        $cart = $this->getCart();
        $key  = $productId . '_' . $size;

        if (isset($cart[$key])) {
            unset($cart[$key]);
            $this->saveCart($cart);
        }
    }

    /**
     * Vide le panier.
     * 
     * @return void
     */
    public function clearCart(): void
    {
        $this->session->remove(self::CART_KEY);
    }

    /**
     * Retourne le contenu détaillé du panier avec les entités Product.
     *
     * Chaque élément retourné contient :
     * - product   : l'entité Product
     * - size      : la taille choisie
     * - quantity  : la quantité
     * - lineTotal : le total de la ligne (prix * quantité)
     *
     * @return array
     */
    public function getItems(): array
    {
        $items = [];
        $cart = $this->getCart();

        foreach ($cart as $row) {
            $product = $this->productRepository->find($row['productId']);

            // If the product is no longer available or no longer exists in the database
            if (!$product instanceof Product) {
                continue;
            }

            $items[] = [
                'product' => $product,
                'size' => $row['size'],
                'quantity' => $row['quantity'],
                'lineTotal' => $product->getPrice() * $row['quantity'], 
            ];
        }

        return $items;
    }

    /**
     * Calcule le montant total du panier.
     * 
     * @return float
     */
    public function getTotal(): float
    {
        $total = 0.0;

        foreach ($this->getItems() as $item) {
            $total += $item['lineTotal'];
        }

        return $total;
    }

    /**
     * Retourne le panier stocké en session.
     * 
     * @return array
     */
    private function getCart(): array
    {
        return $this->session->get(self::CART_KEY, []);
    }

    /**
     * Enregistre le panier en session.
     * 
     * @param array<string, array{productId:int,size:string,quantity:int}> $cart
     * 
     * @return void
     */
    private function saveCart(array $cart): void
    {
        $this->session->set(self::CART_KEY, $cart);
    }
}
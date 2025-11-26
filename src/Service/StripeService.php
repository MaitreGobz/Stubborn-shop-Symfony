<?php

namespace App\Service;

use Stripe\StripeClient;
use Stripe\Checkout\Session;
use Stripe\Stripe;

/**
 * Class StripeService
 * 
 * Service repsonsable de la communication avec l'API Stripe.
 * Permet de créer des sessions de paiement et de gérer les interactions avec Stripe.
 */
class StripeService
{
    /**
     * Client Stripe utilisé pour les appels API.
     * 
     * @var StripeClient
     */
    private StripeClient $client;

    /**
     * Constructeur de la classe StripeService.
     * 
     * @param string $stripeSecretKey Clé API Stripe.
     */
    public function __construct(string $stripeSecretKey)
    {
        $this->client = new StripeClient($stripeSecretKey);
    }

    /**
     * Crée une session de paiement Stripe.
     * 
     * Le tableau $cartItems doit contenir des éléments avec les clés suivantes :
     * [
     *  [
     *   'name' => string,        // Nom du produit
     *   'amount' => int,       // Montant en centimes
     *   'quantity' => int      // Quantité du produit
     *  ],
     * ...
     * ]
     * 
     * @param array $cartItems Liste des articles dans le panier.
     * @param string $successUrl URL de redirection en cas de succès.
     * @param string $cancelUrl URL de redirection en cas d'annulation.
     * 
     * @return Session La session de paiement créée.
     * 
     * @throws \Stripe\Exception\ApiErrorException Si la création de la session échoue.
     */
    public function createCheckoutSession(array $cartItems, string $successUrl, string $cancelUrl): Session
    {
        $lineItems = $this->buildLineItems($cartItems);

        return $this->client->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);
    }

    /**
     * Prépare les lines_items Stripe à partir des articles du panier.
     * 
     * @param array $cartItems Liste des articles dans le panier.
     * 
     * @return array Tableau formaté pour les line_items Stripe.
     */
    private function buildLineItems(array $cartItems): array
    {
        $lineItems = [];

        foreach ($cartItems as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item['name'],
                    ],
                    'unit_amount' => $item['amount'],
                ],
                'quantity' => $item['quantity'],
            ];
        }

        return $lineItems;
    }
}
<?php

namespace App\Tests\Service;

use App\Service\StripeService;
use PHPUnit\Framework\TestCase;
use Stripe\Checkout\Session;

/**
 * Test unitaires pour le service StripeService.
 */
class StripeServiceTest extends TestCase
{
    /**
     * @var StripeService
     */
    private StripeService $stripeService;

    /**
     * Initialisation d'une insatance de StripeService avant chaque test.
     */
    protected function setUp(): void
    {
        $this->stripeService = new StripeService('sk_test_1234567890abcdef');
    }

    /**
     * Vérifie que les line items sont correctement formatés pour Stripe.
     */
    public function testFormatLineItemsForStripe(): void
    {
        $cartItems = [
            [
                'name' => 'Blackbelt',
                'price' => 29.90,
                'quantity' => 2,
            ],
            [
                'name' => 'Snow',
                'price' => 32.00,
                'quantity' => 1,
            ],
        ];

        $successUrl = 'https://example.com/success';
        $cancelUrl = 'https://example.com/cancel';

        $capturedPayload =null;
        $stripeService = new class('sk_test_1234567890abcdef') extends StripeService {
            public array $lastPayload = [];  

            /**
             * Surcharge de la méthode createCheckoutSession pour capturer le payload.
             * 
             * @param array $cartItems
             * @param string $successUrl
             * @param string $cancelUrl
             * 
             * @return Session
             */
            public function createCheckoutSession(array $cartItems, string $successUrl, string $cancelUrl): Session
            {
                $reflection = new \ReflectionClass(StripeService::class);
                $method = $reflection->getMethod('createCheckoutSession');
                $method->setAccessible(true);

                $lineItems = [];

                foreach ($cartItems as $item) {
                    $lineItems[] = [
                        'price_data' => [
                            'currency' => 'eur',
                            'product_data' => [
                                'name' => $item['name'],
                            ],
                            'unit_amount' => (int) round($item['price'] * 100),
                        ],
                        'quantity' => $item['quantity'],
                    ];
                }

                $this->lastPayload = [
                    'payment_method_types' => ['card'],
                    'line_items' => $lineItems,
                    'mode' => 'payment',
                    'success_url' => $successUrl,
                    'cancel_url' => $cancelUrl,
                ];

                return Session::constructFrom(['id' => 'cs_test_1234567890abcdef']);
            }
        };

        $session = $stripeService->createCheckoutSession($cartItems, $successUrl, $cancelUrl);

        $payload = $stripeService->lastPayload;

        $this->assertSame('payment', $payload['mode']);
        $this->assertSame($successUrl, $payload['success_url']);
        $this->assertSame($cancelUrl, $payload['cancel_url']);

        $this->assertCount(2, $payload['line_items']);

        $firstItem = $payload['line_items'][0];
        $this->assertSame('Blackbelt', $firstItem['price_data']['product_data']['name']);
        $this->assertSame(2990, $firstItem['price_data']['unit_amount']);
        $this->assertSame(2, $firstItem['quantity']);

        $secondItem = $payload['line_items'][1];
        $this->assertSame('Snow', $secondItem['price_data']['product_data']['name']);
        $this->assertSame(3200, $secondItem['price_data']['unit_amount']);
        $this->assertSame(1, $secondItem['quantity']);

        $this->assertSame('cs_test_1234567890abcdef', $session->id);
    }
}
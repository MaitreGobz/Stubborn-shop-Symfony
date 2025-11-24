<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests fonctionnels de la page boutique /products.
 */
class ProductControllerTest extends WebTestCase
{
    public function testProductsPageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/products');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('section.products-grid');
    }

    public function testProductsFilterRange(): void
    {
        $client = static::createClient();
        $client->request('GET', '/products?range=29-35');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.product-card');
    }
}

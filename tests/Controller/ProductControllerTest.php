<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests fonctionnels de la page boutique /products.
 */
class ProductControllerTest extends WebTestCase
{
    private function loginExistingUser($client): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => 'login-test@example.com']);

        $this->assertNotNull($user, "L'utilisateur login-test@example.com n'existe pas en base test.");

        $client->loginUser($user, 'main');
    }
    
    public function testProductsPageLoads(): void
    {
        $client = static::createClient();
        $this->loginExistingUser($client);

        $client->request('GET', '/products');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('section.products-grid');
    }

    public function testProductsFilterRange(): void
    {
        $client = static::createClient();
        $this->loginExistingUser($client);

        $client->request('GET', '/products?range=29-35');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.product-card');
    }
}

<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test fonctionnel : vérifie que la page d'accueil charge correctement
 * et que le menu est affiché.
 */
class HomeControllerTest extends WebTestCase
{
    public function testHomePageLoadsCorrectly(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('header');
        $this->assertSelectorTextContains('nav', 'Accueil');
    }
}

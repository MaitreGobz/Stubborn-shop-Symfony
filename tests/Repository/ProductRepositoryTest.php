<?php

namespace App\Tests\Repository;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @covers \App\Repository\ProductRepository
 *
 * Tests du repository Product (filtrage par prix).
 */
class ProductRepositoryTest extends KernelTestCase
{
    /**
     * Teste que findByPriceRange retourne bien un tableau de produits
     * et respecte les bornes.
     */
    public function testFindByPriceRange(): void
    {
        self::bootKernel();
        $repo = self::getContainer()->get(ProductRepository::class);

        $products = $repo->findByPriceRange('10-29');

        $this->assertIsArray($products);

        foreach ($products as $product) {
            $this->assertGreaterThanOrEqual(10, $product->getPrice());
            $this->assertLessThanOrEqual(29, $product->getPrice());
        }
    }
}

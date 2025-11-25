<?php

namespace App\Tests\Repository;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
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

    /**
     * Teste la récupération des produits mis en avant.
     */
    public function testFindFeaturedProducts(): void
    {
        self::bootKernel();
        $productRepository = self::getContainer()->get(ProductRepository::class);

        $featuredProducts = $productRepository->findFeatured(3);

        $this->assertCount(3, $featuredProducts);
        foreach ($featuredProducts as $product) {
            $this->assertTrue($product->isFeatured());
        }
    }
}

<?php

namespace App\Tests\Service;

use App\Entity\Product;
use App\Service\CartService;
use App\Repository\ProductRepository;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Test unitaires pour le service CartService.
 */
class CartServiceTest extends TestCase
{
    /**
     * Référence vers le repository mocké des produits.
     *
     * @var ProductRepository&MockObject
     */
    private ProductRepository $productRepository;

    /**
     * Tableau de produits de test indexés par leur identifiant.
     *
     * @var array<int, Product>
     */
    private array $products = [];

    /**
     * Crée une instance de CartService avec RequestStack et ProductRepository mockés.
     *
     * @return CartService
     */
    private function createCartService(): CartService
    {
        // Session in memory
        $session = new Session(new MockArraySessionStorage());

        // False request with the session
        $request = new Request();
        $request->setSession($session);

        // RequestStack containing our request
        $requestStack = new RequestStack();
        $requestStack->push($request);

        // ProductRepository mockup
        /** @var ProductRepository&MockObject $productRepository */
        $productRepository = $this->createMock(ProductRepository::class);

        // When CartService will do $productRepository->find($id),
        // we will return the product stored in $this->products[$id]
        $productRepository
            ->method('find')
            ->willReturnCallback(function (int $id): ?Product {
                return $this->products[$id] ?? null;
            });

        $this->productRepository = $productRepository;

        return new CartService($requestStack, $productRepository);
    }

    /**
     * Crée un produit de test (mock) avec un id et un prix donnés.
     *
     * @param int   $id    Identifiant du produit.
     * @param float $price Prix du produit.
     *
     * @return Product
     */
    private function createTestProduct(int $id, float $price): Product
    {
        /** @var Product&MockObject $product */
        $product = $this->createMock(Product::class);

        $product->method('getId')->willReturn($id);
        $product->method('getPrice')->willReturn((string)$price);

        // This product is recorded in the table.
        // so that $productRepository->find($id) can return it.
        $this->products[$id] = $product;

        return $product;
    }

    /**
     * Teste l'ajout d'un produit au panier.
     *
     * @return void
     */
    public function testAddProductToCart(): void
    {
        $cartService = $this->createCartService();
        $product     = $this->createTestProduct(1, 29.99);

        $cartService->addToCart($product, 'M');
        $items = $cartService->getItems();

        $this->assertCount(1, $items);
        $this->assertSame('M', $items[0]['size']);
        $this->assertSame(1, $items[0]['quantity']);
        $this->assertEquals(29.99, (float) $items[0]['product']->getPrice());
        $this->assertEquals(29.99, $items[0]['lineTotal']);
    }

    /**
     * Vérifie que l'ajout d'un produit existant incrémente la quantité.
     *
     * @return void
     */
    public function testAddExistingProductIncrementsQuantity(): void
    {
        $cartService = $this->createCartService();
        $product     = $this->createTestProduct(1, 29.99);

        $cartService->addToCart($product, 'M');
        $cartService->addToCart($product, 'M');

        $items = $cartService->getItems();

        $this->assertCount(1, $items);
        $this->assertSame(2, $items[0]['quantity']);
        $this->assertEquals(29.99 * 2, $items[0]['lineTotal']);
    }

    /**
     * Teste le calcul du total du panier.
     *
     * @return void
     */
    public function testGetTotal(): void
    {
        $cartService = $this->createCartService();

        $product1 = $this->createTestProduct(1, 10.00);
        $product2 = $this->createTestProduct(2, 15.50);

        $cartService->addToCart($product1, 'S'); // 10.00
        $cartService->addToCart($product2, 'M'); // 15.50
        $cartService->addToCart($product2, 'M'); // 15.50

        $total = $cartService->getTotal();

        $this->assertEquals(41.00, $total);
    }

    /**
     * Teste la suppression d'un produit du panier.
     *
     * @return void
     */
    public function testRemoveProductFromCart(): void
    {
        $cartService = $this->createCartService();
        $product     = $this->createTestProduct(1, 29.90);

        $cartService->addToCart($product, 'M');
        $this->assertCount(1, $cartService->getItems());

        // removeItem expects a productId + a size
        $cartService->removeItem($product->getId(), 'M');

        $items = $cartService->getItems();

        $this->assertCount(0, $items);
        $this->assertEquals(0.0, $cartService->getTotal());
    }

}

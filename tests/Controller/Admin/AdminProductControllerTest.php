<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Entity\Product;
use App\Entity\User;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


/**
 * Tests fonctionnels pour le contrôleur d'administration des produits.
 * 
 * @package App\Tests\Controller\Admin
 */
class AdminProductControllerTest extends WebTestCase
{
    /**
     * Crée et persiste un utilisateur administrateur pour les tests.
     *
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités Doctrine.
     *
     * @return User Utilisateur avec le rôle ROLE_ADMIN.
     */
    private function createAdminUser(EntityManagerInterface $entityManager): User
    {
        $userRepository = $entityManager->getRepository(User::class);

        $existing = $userRepository->findOneBy(['email' => 'admin@test.com']);
        if ($existing instanceof User) {
            return $existing;
        }

        $user = new User();
        $user->setEmail('admin@test.com');
        $user->setPassword('test');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setName('Admin Test');
        $user->setDeliveryAddress('1 rue de Test, 06000 Nice');
        if (method_exists($user, 'setIsVerified')) {
            $user->setIsVerified(true);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }

    /**
     * Crée et persiste un produit pour les tests.
     *
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités Doctrine.
     *
     * @return Product Produit persistant en base de données.
     */
    private function createTestProduct(EntityManagerInterface $entityManager): Product
    {
        $product = new Product();
        $product->setName('Produit de test');
        $product->setPrice('29.99');
        $product->setStockXs(5);
        $product->setStockS(5);
        $product->setStockM(5);
        $product->setStockL(5);
        $product->setStockXl(5);

        $entityManager->persist($product);
        $entityManager->flush();

        return $product;
    }

    /**
     * Teste que l'accès à la route /admin/product/new
     * est refusé pour un utilisateur non connecté.
     *
     * @return void
     */
    public function testNewProductAccessDeniedForAnonymousUser(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin/product/new');

        $this->assertTrue(
            $client->getResponse()->isRedirection(),
            'Un utilisateur anonyme doit être redirigé (non autorisé à accéder à /admin/product/new).'
        );
    }

    /**
     * Teste que la route /admin/product/new est accessible
     * pour un utilisateur avec le rôle ROLE_ADMIN.
     *
     * @return void
     */
    public function testNewProductAccessGrantedForAdmin(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        /** 
         * @var EntityManagerInterface $entityManager 
         */
        $entityManager = $container->get(EntityManagerInterface::class);

        $adminUser = $this->createAdminUser($entityManager);
        $client->loginUser($adminUser);

        $client->request('POST', '/admin/product/new');

        $this->assertResponseRedirects('/admin/');
    }

    /**
     * Teste la suppression d'un produit pour un administrateur.
     *
     * @return void
     */
    public function testAdminCanDeleteProduct(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        /** 
         * @var EntityManagerInterface $entityManager 
         */
        $entityManager = $container->get(EntityManagerInterface::class);

        /** 
         * @var ProductRepository $productRepository 
         */
        $productRepository = $container->get(ProductRepository::class);

        $adminUser = $this->createAdminUser($entityManager);
        $client->loginUser($adminUser);

        $product = $this->createTestProduct($entityManager);
        $productId = $product->getId();

        $crawler = $client->request('GET', '/admin/');
        $this->assertResponseIsSuccessful();

        $form = $crawler
            ->filter(sprintf('form[action="/admin/product/%d"]', $productId))
            ->form();

        $client->submit($form);

        $this->assertResponseRedirects('/admin/');

        $deletedProduct = $productRepository->find($productId);
        $this->assertNull(
            $deletedProduct,
            'Le produit doit être supprimé de la base après la soumission du formulaire de suppression.'
        );
    }

    /**
     * Teste que l'édition d'un produit fonctionne pour un administrateur.
     *
     * @return void
     */
    public function testAdminCanEditProduct(): void
    {
        $client = static::createClient();

        $container = static::getContainer();

        /** 
         * @var EntityManagerInterface $entityManager 
         */
        $entityManager = $container->get(EntityManagerInterface::class);

        /** 
         * @var ProductRepository $productRepository 
         */
        $productRepository = $container->get(ProductRepository::class);

        $adminUser = $this->createAdminUser($entityManager);
        $client->loginUser($adminUser);

        $product = $this->createTestProduct($entityManager);
        $productId = $product->getId();

        $crawler = $client->request('GET', '/admin/');
        $this->assertResponseIsSuccessful();

        $form = $crawler
            ->filter(sprintf('form[action="/admin/product/%d/edit"]', $productId))
            ->form();

        $form['product_form[name]']    = 'Produit édité';
        $form['product_form[price]']   = '39.99';
        $form['product_form[stockXs]'] = 10;
        $form['product_form[stockS]']  = 11;
        $form['product_form[stockM]']  = 12;
        $form['product_form[stockL]']  = 13;
        $form['product_form[stockXl]'] = 14;

        $client->submit($form);

        $this->assertResponseRedirects('/admin/');

        $updatedProduct = $productRepository->find($productId);
        $this->assertInstanceOf(Product::class, $updatedProduct);

        $this->assertSame('Produit édité', $updatedProduct->getName());
        $this->assertSame('39.99', $updatedProduct->getPrice());
        $this->assertSame(10, $updatedProduct->getStockXs());
        $this->assertSame(11, $updatedProduct->getStockS());
        $this->assertSame(12, $updatedProduct->getStockM());
        $this->assertSame(13, $updatedProduct->getStockL());
        $this->assertSame(14, $updatedProduct->getStockXl());
    }
}
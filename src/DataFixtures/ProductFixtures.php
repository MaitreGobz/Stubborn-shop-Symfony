<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;

class ProductFixtures extends Fixture
{
    /**
     * Charge en base les sweat-shirts Stubborn.
     * Les produits marqués "**" sont mis en avant sur la home (featured=true).
     *
     * @param ObjectManager $manager Gestionnaire Doctrine pour la persistance.
     */
    public function load(ObjectManager $manager): void
    {
        $products = [
            //name, price, image, featured
            ['Blackbelt', '29.90', '1.jpeg', true],
            ['Bluebelt', '29.90', '2.jpeg', false],
            ['Street', '34.50', '3.jpeg', false],
            ['Pokeball', '45.00', '4.jpeg', true],
            ['PinkLady', '29.90', '5.jpeg', false],
            ['Snow', '32.00', '6.jpeg', false],
            ['Greyback', '28.50', '7.jpeg', false],
            ['BlueCloud', '45.00', '8.jpeg', false],
            ['BornInUsa', '59.90', '9.jpeg', true],
            ['GreenSchool', '42.20', '10.jpeg', false],
        ];

        foreach ($products as [$name, $price, $image, $featured]) {
            $product = new Product();
            $product->setName($name);
            $product->setPrice($price);
            $product->setImage($image);
            $product->setFeatured($featured);
            $product->setStockXs(2);
            $product->setStockS(2);
            $product->setStockM(2);
            $product->setStockL(2);
            $product->setStockXl(2);

            $manager->persist($product);
        }
        

        $manager->flush();
    }
}

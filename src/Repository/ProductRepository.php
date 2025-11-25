<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

        /**
         * Retourne une liste de produits mis en avant pour la page d'accueil.
         *
         * @param int $limit Nombre maximum de produits à retourner.
         * 
         * @return Product[] Liste de produits featured.
         */
       public function findFeatured(int $limit = 3): array
       {
           return $this->createQueryBuilder('p')
               ->andWhere('p.featured = :featured')
               ->setParameter('featured', true)
               ->orderBy('p.id', 'ASC')
               ->setMaxResults($limit)
               ->getQuery()
               ->getResult()
           ;
       }

       /**
        * Retourne la liste des produits filtrés par fourchette de prix.
        *
        * Fourchettes gérées :
        * - "10-29"
        * - "29-35"
        * - "35-50"
        * - null => aucun filtre (tous les produits)
        *
        * @param string|null $range Fourchette de prix.
        * @param float $min Prix minimum inclus.
        * @param float $max Prix maximum inclus.
        *
        * @return Product[] Liste des produits filtrés.
        */
       public function findByPriceRange(?string $range): array
       {
        $productQuery = $this->createQueryBuilder('p')->orderBy('p.price', 'ASC');

        // No filter → returns all products
        if(!$range) {
            return $productQuery->getQuery()->getResult();
        }

        // Price filtering terminal
        $allowedRanges = [
            '10-29' => [10, 29],
            '29-35' => [29, 35],
            '35-50' => [35, 50],
        ];

        // Safety: If the range is unknown, return all products
        if(!array_key_exists($range, $allowedRanges)) {
            return $productQuery->getQuery()->getResult();
        }

        [$min, $max] = $allowedRanges[$range];

        return $productQuery
            ->andWhere('p.price >= :min')
            ->andWhere('p.price <= :max')
            ->setParameter('min', $min)
            ->setParameter('max', $max)
            ->getQuery()
            ->getResult();
       }
}

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

}

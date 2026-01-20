<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Articulo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticuloRepositorio extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Articulo::class);
    }

    public function filtrarPorCategoriaYFecha(?int $categoriaId, ?\DateTimeImmutable $desde,
    ?\DateTimeImmutable $hasta, ?string $q, $usuarioId): array 
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.categoria', 'c')
            ->addSelect('c')
            ->leftJoin('a.usuario', 'u')
            ->addSelect('u')
            ->orderBy('a.fechaCreacion', 'DESC');

        if ($categoriaId) {
            $qb->andWhere('a.categoria = :cat')
            ->setParameter('cat', $categoriaId);
        }

        if ($desde) {
            $qb->andWhere('a.fechaCreacion >= :desde')
            ->setParameter('desde', $desde);
        }

        if ($hasta) {
            // Para incluir todo el día "hasta", lo llevamos a 23:59:59
            $hastaFinDia = $hasta->setTime(23, 59, 59);

            $qb->andWhere('a.fechaCreacion <= :hasta')
            ->setParameter('hasta', $hastaFinDia);
        }

        if ($q) {
            $qb->andWhere(
                $qb->expr()->orX(
                    'a.titulo LIKE :q',
                    'a.descripcion LIKE :q'
                )
            )
            ->setParameter('q', '%' . $q . '%');
        }

        if ($usuarioId) {
            $qb->andWhere('a.usuario = :usuario')
            ->setParameter('usuario', $usuarioId);
        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Article[] Returns an array of Article objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Article
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

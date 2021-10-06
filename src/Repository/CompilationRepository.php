<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Compilation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;

/**
 * @method null|Compilation find($id, $lockMode = null, $lockVersion = null)
 * @method null|Compilation findOneBy(array $criteria, array $orderBy = null)
 * @method Compilation[] findAll()
 * @method Compilation[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompilationRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Compilation::class);
    }

    /**
     * @return Query
     */
    public function indexQuery() {
        return $this->createQueryBuilder('compilation')
            ->orderBy('compilation.id')
            ->getQuery()
        ;
    }

    /**
     * @param string $q
     *
     * @return Collection|Compilation[]
     */
    public function typeaheadQuery($q) {
        throw new RuntimeException('Not implemented yet.');
        $qb = $this->createQueryBuilder('compilation');
        $qb->andWhere('compilation.column LIKE :q');
        $qb->orderBy('compilation.column', 'ASC');
        $qb->setParameter('q', "{$q}%");

        return $qb->getQuery()->execute();
    }

    /**
     * @param string $q
     *
     * @return Collection|Compilation[]|Query
     */
    public function searchLabelQuery($q) {
        $qb = $this->createQueryBuilder('compilation');
        $qb->addSelect('MATCH (compilation.label) AGAINST(:q BOOLEAN) as HIDDEN score');
        $qb->andHaving('score > 0');
        $qb->orderBy('score', 'DESC');
        $qb->setParameter('q', $q);

        return $qb->getQuery();
    }

    /**
     * @param string $q
     *
     * @return Collection|Compilation[]|Query
     */
    public function searchDescriptionQuery($q) {
        $qb = $this->createQueryBuilder('compilation');
        $qb->addSelect('MATCH (compilation.description) AGAINST(:q BOOLEAN) as HIDDEN score');
        $qb->andHaving('score > 0');
        $qb->orderBy('score', 'DESC');
        $qb->setParameter('q', $q);

        return $qb->getQuery();
    }

    /**
     * @param string $q
     *
     * @return Collection|Compilation[]|Query
     */
    public function searchLabelDescriptionQuery($q) {
        $qb = $this->createQueryBuilder('compilation');
        $qb->addSelect('MATCH (compilation.label, compilation.description) AGAINST(:q BOOLEAN) as HIDDEN score');
        $qb->andHaving('score > 0');
        $qb->orderBy('score', 'DESC');
        $qb->setParameter('q', $q);

        return $qb->getQuery();
    }
}

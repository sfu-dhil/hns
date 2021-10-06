<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Scrapbook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;

/**
 * @method null|Scrapbook find($id, $lockMode = null, $lockVersion = null)
 * @method null|Scrapbook findOneBy(array $criteria, array $orderBy = null)
 * @method Scrapbook[] findAll()
 * @method Scrapbook[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScrapbookRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Scrapbook::class);
    }

    /**
     * @return Query
     */
    public function indexQuery() {
        return $this->createQueryBuilder('scrapbook')
            ->orderBy('scrapbook.id')
            ->getQuery()
        ;
    }

    /**
     * @param string $q
     *
     * @return Collection|Scrapbook[]
     */
    public function typeaheadQuery($q) {
        throw new RuntimeException('Not implemented yet.');
        $qb = $this->createQueryBuilder('scrapbook');
        $qb->andWhere('scrapbook.column LIKE :q');
        $qb->orderBy('scrapbook.column', 'ASC');
        $qb->setParameter('q', "{$q}%");

        return $qb->getQuery()->execute();
    }

    /**
     * @param string $q
     *
     * @return Collection|Query|Scrapbook[]
     */
    public function searchLabelQuery($q) {
        $qb = $this->createQueryBuilder('scrapbook');
        $qb->addSelect('MATCH (scrapbook.label) AGAINST(:q BOOLEAN) as HIDDEN score');
        $qb->andHaving('score > 0');
        $qb->orderBy('score', 'DESC');
        $qb->setParameter('q', $q);

        return $qb->getQuery();
    }

    /**
     * @param string $q
     *
     * @return Collection|Query|Scrapbook[]
     */
    public function searchDescriptionQuery($q) {
        $qb = $this->createQueryBuilder('scrapbook');
        $qb->addSelect('MATCH (scrapbook.description) AGAINST(:q BOOLEAN) as HIDDEN score');
        $qb->andHaving('score > 0');
        $qb->orderBy('score', 'DESC');
        $qb->setParameter('q', $q);

        return $qb->getQuery();
    }

    /**
     * @param string $q
     *
     * @return Collection|Query|Scrapbook[]
     */
    public function searchLabelDescriptionQuery($q) {
        $qb = $this->createQueryBuilder('scrapbook');
        $qb->addSelect('MATCH (scrapbook.label, scrapbook.description) AGAINST(:q BOOLEAN) as HIDDEN score');
        $qb->andHaving('score > 0');
        $qb->orderBy('score', 'DESC');
        $qb->setParameter('q', $q);

        return $qb->getQuery();
    }
}

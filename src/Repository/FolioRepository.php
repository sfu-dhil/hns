<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Folio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;

/**
 * @method null|Folio find($id, $lockMode = null, $lockVersion = null)
 * @method null|Folio findOneBy(array $criteria, array $orderBy = null)
 * @method Folio[] findAll()
 * @method Folio[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FolioRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Folio::class);
    }

    /**
     * @return Query
     */
    public function indexQuery() {
        return $this->createQueryBuilder('folio')
            ->orderBy('folio.id')
            ->getQuery()
        ;
    }

    /**
     * @param string $q
     *
     * @return Collection|Folio[]
     */
    public function typeaheadQuery($q) {
        throw new RuntimeException('Not implemented yet.');
        $qb = $this->createQueryBuilder('folio');
        $qb->andWhere('folio.column LIKE :q');
        $qb->orderBy('folio.column', 'ASC');
        $qb->setParameter('q', "{$q}%");

        return $qb->getQuery()->execute();
    }

    /**
     * @param string $q
     *
     * @return Collection|Folio[]|Query
     */
    public function searchTextQuery($q) {
        $qb = $this->createQueryBuilder('folio');
        $qb->addSelect('MATCH (folio.text) AGAINST(:q BOOLEAN) as HIDDEN score');
        $qb->andHaving('score > 0');
        $qb->orderBy('score', 'DESC');
        $qb->setParameter('q', $q);

        return $qb->getQuery();
    }
}

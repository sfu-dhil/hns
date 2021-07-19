<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\DocumentSet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;

/**
 * @method null|DocumentSet find($id, $lockMode = null, $lockVersion = null)
 * @method null|DocumentSet findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentSet[] findAll()
 * @method DocumentSet[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentSetRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, DocumentSet::class);
    }

    /**
     * @return Query
     */
    public function indexQuery() {
        return $this->createQueryBuilder('documentSet')
            ->orderBy('documentSet.id')
            ->getQuery()
        ;
    }

    /**
     * @param string $q
     *
     * @return Collection|DocumentSet[]
     */
    public function typeaheadQuery($q) {
        throw new RuntimeException('Not implemented yet.');
        $qb = $this->createQueryBuilder('documentSet');
        $qb->andWhere('documentSet.column LIKE :q');
        $qb->orderBy('documentSet.column', 'ASC');
        $qb->setParameter('q', "{$q}%");

        return $qb->getQuery()->execute();
    }
}

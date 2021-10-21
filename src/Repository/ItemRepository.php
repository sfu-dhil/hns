<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Nines\DublinCoreBundle\Entity\Value;
use RuntimeException;

/**
 * @method null|Item find($id, $lockMode = null, $lockVersion = null)
 * @method null|Item findOneBy(array $criteria, array $orderBy = null)
 * @method Item[] findAll()
 * @method Item[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Item::class);
    }

    /**
     * @return Query
     */
    public function indexQuery() {
        return $this->createQueryBuilder('item')
            ->orderBy('item.id')
            ->getQuery()
        ;
    }

    /**
     * @param string $q
     *
     * @return Collection|Item[]
     */
    public function typeaheadQuery($q) {
        $cls = Item::class;
        $qb = $this->createQueryBuilder('item');
        $qb->innerJoin(Value::class, 'value', Query\Expr\Join::WITH, "value.entity = concat('{$cls}:', item.id)");
        $qb->addSelect('MATCH(value.data) AGAINST(:q BOOLEAN) as HIDDEN dc_score');
        $qb->orderBy('dc_score', 'ASC');
        $qb->setParameter('q', "{$q}%");

        return $qb->getQuery()->execute();
    }

    /**
     * @param string $q
     *
     * @return Collection|Item[]|Query
     */
    public function searchQuery($q) {
        $cls = Item::class;
        $qb = $this->createQueryBuilder('item');
        $qb->innerJoin(Value::class, 'value', Query\Expr\Join::WITH, "value.entity = concat('{$cls}:', item.id)");
        $qb->addSelect('MATCH(value.data) AGAINST(:q BOOLEAN) as HIDDEN dc_score');
//        $qb->addSelect("(text_score + dc_score) AS HIDDEN score");
//        $qb->andHaving('score > 0');
//        $qb->orderBy('score', 'DESC');
        $qb->setParameter('q', $q);

        return $qb->getQuery();
    }
}

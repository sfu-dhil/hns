<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Nines\DublinCoreBundle\Entity\Value;

/**
 * @method Item|null find($id, $lockMode = null, $lockVersion = null)
 * @method Item|null findOneBy(array $criteria, array $orderBy = null)
 * @method Item[]    findAll()
 * @method Item[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    /**
     * @return Query
     */
    public function indexQuery() {
        return $this->createQueryBuilder('item')
            ->orderBy('item.id')
            ->getQuery();
    }

    /**
     * @param string $q
     *
     * @return Collection|Item[]
     */
    public function typeaheadQuery($q) {
        throw new \RuntimeException("Not implemented yet.");
        $qb = $this->createQueryBuilder('item');
        $qb->andWhere('item.column LIKE :q');
        $qb->orderBy('item.column', 'ASC');
        $qb->setParameter('q', "{$q}%");

        return $qb->getQuery()->execute();
    }

    /**
     * @param string $q
     *
     * @return Query|Collection|Item[]
     */
    public function searchQuery($q) {
        $cls = Item::class;
        $qb = $this->createQueryBuilder('item');
        $qb->innerJoin(Value::class, 'value', Query\Expr\Join::WITH, "value.entity = concat('${cls}:', item.id)");
        $qb->addSelect('MATCH (item.text) AGAINST(:q BOOLEAN) as HIDDEN text_score');
        $qb->addSelect("MATCH(value.data) AGAINST(:q BOOLEAN) as HIDDEN dc_score");
//        $qb->addSelect("(text_score + dc_score) AS HIDDEN score");
//        $qb->andHaving('score > 0');
//        $qb->orderBy('score', 'DESC');
        $qb->setParameter('q', $q);

        return $qb->getQuery();
    }

}

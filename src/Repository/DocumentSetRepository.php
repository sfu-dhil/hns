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
use Nines\UtilBundle\Repository\TermRepository;
use RuntimeException;

/**
 * @method null|DocumentSet find($id, $lockMode = null, $lockVersion = null)
 * @method null|DocumentSet findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentSet[] findAll()
 * @method DocumentSet[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentSetRepository extends TermRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, DocumentSet::class);
    }
}

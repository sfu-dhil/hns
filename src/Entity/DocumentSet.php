<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use App\Repository\DocumentSetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * @ORM\Entity(repositoryClass=DocumentSetRepository::class)
 */
class DocumentSet extends AbstractTerm {
    /**
     * @var Compilation
     * @ORM\ManyToOne(targetEntity="Compilation", inversedBy="documentSets")
     */
    private $compilation;

    /**
     * @var Collection|Document[]
     * @ORM\OneToMany(targetEntity="Document", mappedBy="documentSet")
     */
    private $documents;

    public function __construct() {
        parent::__construct();
        $this->documents = new ArrayCollection();
    }
}

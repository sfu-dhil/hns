<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use App\Repository\CompilationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * @ORM\Entity(repositoryClass=CompilationRepository::class)
 */
class Compilation extends AbstractTerm {
    /**
     * @var Collection|DocumentSet[]
     * @ORM\OneToMany(targetEntity="DocumentSet", mappedBy="compilation")
     */
    private $documentSets;

    public function __construct() {
        parent::__construct();
        $this->documentSets = new ArrayCollection();
    }
}

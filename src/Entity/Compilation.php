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
use Nines\MediaBundle\Entity\LinkableInterface;
use Nines\MediaBundle\Entity\LinkableTrait;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * @ORM\Entity(repositoryClass=CompilationRepository::class)
 */
class Compilation extends AbstractTerm implements LinkableInterface {

    use LinkableTrait {
        LinkableTrait::__construct as linkable_constructor;
    }

    /**
     * @var Collection|DocumentSet[]
     * @ORM\OneToMany(targetEntity="DocumentSet", mappedBy="compilation")
     */
    private $documentSets;

    public function __construct() {
        parent::__construct();
        $this->linkable_constructor();
        $this->documentSets = new ArrayCollection();
    }

    /**
     * @return Collection|DocumentSet[]
     */
    public function getDocumentSets() : Collection {
        return $this->documentSets;
    }

    public function addDocumentSet(DocumentSet $documentSet) : self {
        if ( ! $this->documentSets->contains($documentSet)) {
            $this->documentSets[] = $documentSet;
            $documentSet->setCompilation($this);
        }

        return $this;
    }

    public function removeDocumentSet(DocumentSet $documentSet) : self {
        if ($this->documentSets->removeElement($documentSet)) {
            // set the owning side to null (unless already changed)
            if ($documentSet->getCompilation() === $this) {
                $documentSet->setCompilation(null);
            }
        }

        return $this;
    }
}
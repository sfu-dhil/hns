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

    public function getCompilation() : ?Compilation {
        return $this->compilation;
    }

    public function setCompilation(?Compilation $compilation) : self {
        $this->compilation = $compilation;

        return $this;
    }

    /**
     * @return Collection|Document[]
     */
    public function getDocuments() : Collection {
        return $this->documents;
    }

    public function addDocument(Document $document) : self {
        if ( ! $this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setDocumentSet($this);
        }

        return $this;
    }

    public function removeDocument(Document $document) : self {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getDocumentSet() === $this) {
                $document->setDocumentSet(null);
            }
        }

        return $this;
    }
}

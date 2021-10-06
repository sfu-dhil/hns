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
     * @var Collection|Scrapbook[]
     * @ORM\OneToMany(targetEntity="Scrapbook", mappedBy="compilation")
     */
    private $scrapbooks;

    public function __construct() {
        parent::__construct();
        $this->linkable_constructor();
        $this->scrapbooks = new ArrayCollection();
    }

    /**
     * @return Collection|Scrapbook[]
     */
    public function getScrapbooks() : Collection {
        return $this->scrapbooks;
    }

    public function addScrapbook(Scrapbook $scrapbook) : self {
        if ( ! $this->scrapbooks->contains($scrapbook)) {
            $this->scrapbooks[] = $scrapbook;
            $scrapbook->setCompilation($this);
        }

        return $this;
    }

    public function removeScrapbook(Scrapbook $scrapbook) : self {
        if ($this->scrapbooks->removeElement($scrapbook)) {
            // set the owning side to null (unless already changed)
            if ($scrapbook->getCompilation() === $this) {
                $scrapbook->setCompilation(null);
            }
        }

        return $this;
    }
}

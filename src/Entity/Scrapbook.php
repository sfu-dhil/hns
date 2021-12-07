<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use App\Repository\ScrapbookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * @ORM\Entity(repositoryClass=ScrapbookRepository::class)
 */
class Scrapbook extends AbstractTerm {
    /**
     * @ORM\ManyToOne(targetEntity="Compilation", inversedBy="scrapbooks")
     */
    private ?Compilation $compilation;

    /**
     * @var Collection|Item[]
     * @ORM\OneToMany(targetEntity="Item", mappedBy="scrapbook")
     */
    private $items;

    public function __construct() {
        parent::__construct();
        $this->items = new ArrayCollection();
    }

    public function getCompilation() : ?Compilation {
        return $this->compilation;
    }

    public function setCompilation(?Compilation $compilation) : self {
        $this->compilation = $compilation;

        return $this;
    }

    /**
     * @return Collection|Item[]
     */
    public function getItems() : Collection {
        return $this->items;
    }

    public function addItem(Item $item) : self {
        if ( ! $this->items->contains($item)) {
            $this->items[] = $item;
            $item->setScrapbook($this);
        }

        return $this;
    }

    public function removeItem(Item $item) : self {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getScrapbook() === $this) {
                $item->setScrapbook(null);
            }
        }

        return $this;
    }
}

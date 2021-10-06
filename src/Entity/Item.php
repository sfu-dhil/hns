<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Nines\DublinCoreBundle\Entity\ValueInterface;
use Nines\DublinCoreBundle\Entity\ValueTrait;
use Nines\MediaBundle\Entity\AbstractPdf;

/**
 * @ORM\Entity(repositoryClass=ItemRepository::class)
 */
class Item extends Abstractpdf implements ValueInterface {
    use ValueTrait {
        ValueTrait::__construct as value_constructor;

    }

    /**
     * @ORM\ManyToOne(targetEntity="Scrapbook", inversedBy="items")
     */
    private Scrapbook $scrapbook;

    public function __construct() {
        parent::__construct();
        $this->value_constructor();
    }

    public function __toString() : string {
        if ($title = $this->getValues('dc_title')->first()) {
            return $title->getData();
        }

        return 'Untitled #' . $this->id;
    }

    public function getScrapbook() : ?Scrapbook {
        return $this->scrapbook;
    }

    public function setScrapbook(?Scrapbook $scrapbook) : self {
        $this->scrapbook = $scrapbook;

        return $this;
    }
}

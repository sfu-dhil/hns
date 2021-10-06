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
 * @ORM\Table(indexes={
 *     @ORM\Index(name="item_text_ft", columns={"text"}, flags={"fulltext"})
 * })
 */
class Item extends Abstractpdf implements ValueInterface {
    use ValueTrait {
        ValueTrait::__construct as value_constructor;

    }

    /**
     * @var ?string
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity="Scrapbook", inversedBy="items")
     */
    private ?Scrapbook $scrapbook;

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

    public function getText() : ?string {
        return $this->text;
    }

    public function setText(?string $text) : self {
        $this->text = $text;

        return $this;
    }
}

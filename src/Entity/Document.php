<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use App\Repository\DocumentRepository;
use Doctrine\ORM\Mapping as ORM;
use Nines\DublinCoreBundle\Entity\ValueInterface;
use Nines\DublinCoreBundle\Entity\ValueTrait;
use Nines\MediaBundle\Entity\PdfContainerInterface;
use Nines\MediaBundle\Entity\PdfContainerTrait;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * @ORM\Entity(repositoryClass=DocumentRepository::class)
 */
class Document extends AbstractEntity implements PdfContainerInterface, ValueInterface {
    use PdfContainerTrait {
        PdfContainerTrait::__construct as pdf_constructor;
    }
    use ValueTrait {
        ValueTrait::__construct as value_constructor;
    }

    /**
     * @var DocumentSet
     * @ORM\ManyToOne(targetEntity="DocumentSet", inversedBy="documents")
     */
    private $documentSet;

    public function __construct() {
        parent::__construct();
        $this->pdf_constructor();
        $this->value_constructor();
    }

    /**
     * {@inheritDoc}
     */
    public function __toString() : string {
        $titles = $this->getValues('dc_title');
        if($titles && count($titles) > 0) {
            return implode(" ", $titles->toArray());
        }
        return "Untitled #" . $this->getId();
    }

    public function getDocumentSet() : ?DocumentSet {
        return $this->documentSet;
    }

    public function setDocumentSet(?DocumentSet $documentSet) : self {
        $this->documentSet = $documentSet;

        return $this;
    }
}

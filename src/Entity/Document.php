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
use Nines\MediaBundle\Entity\PdfContainerInterface;
use Nines\MediaBundle\Entity\PdfContainerTrait;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * @ORM\Entity(repositoryClass=DocumentRepository::class)
 */
class Document extends AbstractEntity implements PdfContainerInterface {
    use PdfContainerTrait;

    /**
     * @var DocumentSet
     * @ORM\ManyToOne(targetEntity="DocumentSet", inversedBy="documents")
     */
    private $documentSet;

    public function __construct() {
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    public function __toString() : string {
        // TODO: Implement __toString() method.
    }
}

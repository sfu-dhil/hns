<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use App\Repository\FolioRepository;
use Doctrine\ORM\Mapping as ORM;
use RuntimeException;
use Nines\MediaBundle\Entity\ImageContainerInterface;
use Nines\MediaBundle\Entity\ImageContainerTrait;
use Nines\UtilBundle\Entity\AbstractEntity;
use Soundasleep\Html2Text;
use Soundasleep\Html2TextException;

/**
 * @ORM\Entity(repositoryClass=FolioRepository::class)
 * @ORM\Table(indexes={
 *     @ORM\Index(name="folio_text_ft", columns={"text"}, flags={"fulltext"})
 * })
 */
class Folio extends AbstractEntity implements ImageContainerInterface {
    use ImageContainerTrait;

    public const DRAFT = 'draft';

    public const COMPLETE = 'complete';

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private ?int $pageNumber;

    /**
     * @ORM\Column(type="string", length=10, nullable=false)
     */
    private string $status;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $text;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $hocr;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Item", inversedBy="folios")
     */
    private ?Item $item;

    public function __construct() {
        parent::__construct();
        $this->status = self::DRAFT;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString() : string {
        return $this->item . ' page ' . $this->pageNumber ?? '?';
    }

    public function getPageNumber() : ?int {
        return $this->pageNumber;
    }

    public function setPageNumber($pageNumber) : self {
        $this->pageNumber = $pageNumber;

        return $this;
    }

    public function getStatus() : ?string {
        return $this->status;
    }

    public function setStatus(string $status) : self {
        $this->status = $status;

        return $this;
    }

    public function getText() : ?string {
        return $this->text;
    }

    public function setText(?string $text) : self {
        throw new RuntimeException("The text propert of folios is read-only. Edit the OCR instead.");
    }

    public function getHocr() : ?string {
        return $this->hocr;
    }

    /**
     * @throws Html2TextException
     */
    public function setHocr(?string $hocr) : self {
        $this->hocr = $hocr;
        $this->text = Html2Text::convert($this->hocr, ['ignore_errors' => true, 'drop_links' => true]);
        return $this;
    }

    public function getItem() : ?Item {
        return $this->item;
    }

    public function setItem(?Item $item) : self {
        $this->item = $item;

        return $this;
    }
}

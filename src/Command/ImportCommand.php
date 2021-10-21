<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Entity\Compilation;
use App\Entity\Folio;
use App\Entity\Item;
use App\Entity\Scrapbook;
use App\Repository\CompilationRepository;
use App\Repository\ScrapbookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Nines\DublinCoreBundle\Entity\Element;
use Nines\DublinCoreBundle\Entity\Value;
use Nines\DublinCoreBundle\Repository\ElementRepository;
use Nines\MediaBundle\Entity\Image;
use Nines\MediaBundle\Service\PdfManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImportCommand extends Command {
    private CompilationRepository $compilationRepository;

    private EntityManagerInterface $em;

    private ScrapbookRepository $scrapbookRepository;

    private ElementRepository $elementRepository;

    private PdfManager $pdfManager;

    protected static $defaultName = 'app:import';

    protected static $defaultDescription = 'Add a short description for your command';

    private function addItemPages(Item $item, $itemPagination, $ocrDir) : void {
        foreach($itemPagination['pages'] as $p) {
            $basename = basename($itemPagination['source'], '.pdf');
            $imagePath = "{$ocrDir}/{$basename}-{$p}.png";
            $hocrPath = "{$ocrDir}/{$basename}-{$p}.hocr";
            $textPath = "{$ocrDir}/{$basename}-{$p}.txt";

            $folio = new Folio();
            $folio->setItem($item);
            $folio->setPageNumber($p);
            $folio->setText(file_get_contents($textPath));
            $folio->setHocr(file_get_contents($hocrPath));
            $this->em->persist($folio);
            $this->em->flush();

            $upload = new UploadedFile($imagePath, basename($imagePath), 'image/png', null, true);
            $image = new Image();
            $image->setFile($upload);
            $image->setPublic(false);
            $image->setEntity($folio);

            $this->em->persist($image);
            $this->em->flush();

        }
    }

    protected function configure() : void {
        $this->setDescription(self::$defaultDescription);
        $this->addArgument('pages', InputArgument::REQUIRED, 'CSV file with pagination data');
        $this->addArgument('meta', InputArgument::REQUIRED, 'CSV file with metadata');
        $this->addArgument('itemPath', InputArgument::REQUIRED, 'Directory with the segmented PDFs');
        $this->addArgument('ocrDir', InputArgument::REQUIRED, 'Directory with the page images, text, and OCR files');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int {
        $pagination = $this->getPaginationData($input->getArgument('pages'));
        $meta = $this->getMetadata($input->getArgument('meta'));

        $itemPath = $input->getArgument('itemPath');
        $items = $this->getItems($itemPath);

        $ocrDir = $input->getArgument('ocrDir');

        foreach ($items as $item) {
            $output->writeln($item);
            $identifier = preg_replace('/_p.*.pdf$/', '', $item);
            $itemMetadata = $meta[$identifier];
            $itemPagination = $pagination[$item];
            $compilation = $this->findCompilation(explode(',', $itemMetadata[0])[0]);
            $scrapbook = $this->findScrapbook($itemMetadata[1], $compilation, $itemMetadata);
            $item = $this->createItem($itemMetadata, $itemPagination, $scrapbook, $itemPath . '/' . $item);
            $this->addItemPages($item, $itemPagination, $ocrDir);
        }

        return 0;
    }

    public function getItems(string $path) : array {
        $handle = opendir($path);
        $files = [];
        while (false !== ($entry = readdir($handle))) {
            if ( ! preg_match('/\.pdf$/', $entry)) {
                continue;
            }
            $files[] = $entry;
        }
        closedir($handle);

        return $files;
    }

    public function trim(array $row, int $columns) : array {
        $data = array_pad($row, $columns, '');

        return array_map(fn ($d) => preg_replace('/^\s+|\s+$/u', '', $d), $data);
    }

    public function getPaginationData(string $path) : array {
        $handle = fopen($path, 'r');
        for ($i = 0; $i < 2; $i++) {
            fgetcsv($handle); // skip two lines of headers.
        }

        $items = [];
        while ($record = fgetcsv($handle)) {
            $range = preg_replace('/;\s*/', ',', $record[1]);
            $marker = preg_replace('/;\s*/', 'p', $record[1]);
            $part = basename($record[0], '.pdf') . '_p' . $marker . '.pdf';

            $items[$part] = [
                'source' => $record[0],
                'pages' => [],
                'title' => $record[2],
                'date' => $record[3],
                'type' => $record[4],
            ];
            foreach (explode(',', $range) as $r) {
                if (preg_match('/-/', $r)) {
                    [$a, $b] = explode('-', $r);
                    for ($i = (int) $a; $i <= $b; $i++) {
                        $items[$part]['pages'][] = $i;
                    }
                } else {
                    $items[$part]['pages'][] = (int) $r;
                }
            }
        }
        fclose($handle);

        return $items;
    }

    public function getMetadata(string $path) : array {
        $handle = fopen($path, 'r');
        for ($i = 0; $i < 2; $i++) {
            fgetcsv($handle); // skip two lines of headers.
        }
        $metadata = [];
        while ($record = fgetcsv($handle)) {
            $metadata[$record[2]] = $this->trim($record, 52);
        }
        fclose($handle);

        return $metadata;
    }

    public function findCompilation(string $name) : Compilation {
        $compilation = $this->compilationRepository->findOneBy(['label' => $name]);
        if ( ! $compilation) {
            $compilation = new Compilation();
            $compilation->setLabel($name);
            $this->em->persist($compilation);
        }

        return $compilation;
    }

    public function findScrapbook(string $name, Compilation $compilation, array $meta) : Scrapbook {
        $scrapbook = $this->scrapbookRepository->findOneBy(['label' => $name]);
        if ( ! $scrapbook) {
            $scrapbook = new Scrapbook();
            $scrapbook->setLabel($meta[1]);
            $scrapbook->setDescription($meta[0] . "\n\n" . $meta[2]);
            $scrapbook->setCompilation($compilation);
            $this->em->persist($scrapbook);
        }

        return $scrapbook;
    }

    public function getElement(string $name) : Element {
        $element = $this->elementRepository->findOneBy(['name' => $name]);
        if ( ! $element) {
            throw new Exception("Element {$name} not found.");
        }

        return $element;
    }

    public function createValue(Item $item, $name, ...$datas) : void {
        foreach ($datas as $data) {
            if ( ! $data) {
                continue;
            }
            $element = $this->getElement($name);
            $value = new Value();
            $value->setElement($element);
            $value->setData($data);
            $item->addValue($value);
            $this->em->persist($value);
        }
    }

    public function createItem($itemMetadata, $itemPagination, Scrapbook $scrapbook, string $path) : Item {
        $item = new Item();
        $item->setScrapbook($scrapbook);
        $upload = new UploadedFile($path, basename($path), 'application/pdf', null, true);
        $item->setFile($upload);
        $item->setPublic(false);
        $this->pdfManager->uploadFile($item);
        $this->em->persist($item);
        $this->em->flush(); // Item objects must be flushed to the database before adding metadata values.

        $this->createValue($item, 'dc_title', $itemPagination['title']);

        $this->createValue($item, 'dc_identifier', $itemMetadata[2]);
        $this->createValue($item, 'dc_date', $itemMetadata[4], $itemPagination['date']);
        $this->createValue($item, 'dc_publisher', $itemMetadata[6]);
        $this->createValue($item, 'dc_creator', $itemMetadata[7], $itemMetadata[10], $itemMetadata[13], $itemMetadata[16]);
        $this->createValue($item, 'dc_format', $itemMetadata[19]);
        $this->createValue($item, 'dc_language', $itemMetadata[20]);
        $this->createValue($item, 'dc_rights', $itemMetadata[22], $itemMetadata[23]);
        $this->createValue($item, 'dc_source', $itemMetadata[24]);
        $this->createValue($item, 'dc_type', $itemPagination['type']);

        $this->createValue(
            $item,
            'dc_subject',
            $itemMetadata[25],
            $itemMetadata[28],
            $itemMetadata[31],
            $itemMetadata[34],
            $itemMetadata[37],
            $itemMetadata[40],
            $itemMetadata[43],
            $itemMetadata[46],
            $itemMetadata[49]
        );

        return $item;
    }

    /**
     * @required
     */
    public function setCompilationRepository(CompilationRepository $compilationRepository) : void {
        $this->compilationRepository = $compilationRepository;
    }

    /**
     * @required
     */
    public function setScrapbookRepository(ScrapbookRepository $scrapbookRepository) : void {
        $this->scrapbookRepository = $scrapbookRepository;
    }

    /**
     * @required
     */
    public function setElementRepository(ElementRepository $elementRepository) : void {
        $this->elementRepository = $elementRepository;
    }

    /**
     * @required
     */
    public function setEntityManager(EntityManagerInterface $em) : void {
        $this->em = $em;
    }

    /**
     * @required
     */
    public function setPdfManager(PdfManager $pdfManager) : void {
        $this->pdfManager = $pdfManager;
    }
}

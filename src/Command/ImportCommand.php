<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Entity\Compilation;
use App\Entity\Item;
use App\Entity\Scrapbook;
use App\Repository\CompilationRepository;
use App\Repository\ScrapbookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Nines\DublinCoreBundle\Entity\Element;
use Nines\DublinCoreBundle\Entity\Value;
use Nines\DublinCoreBundle\Repository\ElementRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImportCommand extends Command {
    private CompilationRepository $compilationRepository;

    private EntityManagerInterface $em;

    private ScrapbookRepository $scrapbookRepository;

    private ElementRepository $elementRepository;

    protected static $defaultName = 'app:import';

    protected static $defaultDescription = 'Add a short description for your command';

    protected function configure() : void {
        $this->setDescription(self::$defaultDescription)->addArgument('csv', InputArgument::REQUIRED, 'File containing metadata')->addArgument('dir', InputArgument::REQUIRED, 'Directory with PDFs to import');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int {
        $io = new SymfonyStyle($input, $output);

        $csv = $input->getArgument('csv');
        $metadata = $this->getMetadata($csv);

        $dir = $input->getArgument('dir');
        $files = $this->getFileList($dir);

        foreach ($files as $file) {
            $io->writeln($file);
            $this->import($dir, $file, $metadata);
            $this->em->flush();
        }

        return 0;
    }

    public function trim(array $row, int $columns) : array {
        $data = array_pad($row, $columns, '');

        return array_map(fn($d) => preg_replace('/^\s+|\s+$/u', '', $d), $data);
    }

    public function getMetadata(string $file) : array {
        $handle = fopen($file, 'r');
        for ($i = 0; $i < 2; $i++) {
            fgetcsv($handle); // skip two lines of headers.
        }
        $metadata = [];
        while ($record = fgetcsv($handle)) {
            $metadata[$record[2]] = $this->trim($record, 52);
        }

        return $metadata;
    }

    public function getFileList(string $dir) : array {
        $handle = opendir($dir);
        $files = [];
        while (false !== ($entry = readdir($handle))) {
            if ( ! preg_match('/_ocr\.pdf$/', $entry)) {
                continue;
            }
            $files[] = $entry;
        }

        return $files;
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

    public function createValue(Item $item, $name, ...$datas) {
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

    public function createItem(Scrapbook $scrapbook, string $path, array $metadata) : void {
        $item = new Item();
        $item->setScrapbook($scrapbook);
        $upload = new UploadedFile($path, basename($path), 'application/pdf', null, true);
        $item->setFile($upload);
        $item->setPublic(false);
        $text = file_get_contents($path . '.txt');
        $item->setText($text);
        $this->em->persist($item);
        $this->em->flush(); # Item objects must be flushed to the database before adding metadata values.

        $this->createValue($item, 'dc_identifier', $metadata[2]);
        $this->createValue($item, 'dc_date', $metadata[4]);
        $this->createValue($item, 'dc_publisher', $metadata[6]);
        $this->createValue($item, 'dc_creator', $metadata[7], $metadata[10], $metadata[13], $metadata[16]);
        $this->createValue($item, 'dc_format', $metadata[19]);
        $this->createValue($item, 'dc_language', $metadata[20]);
        $this->createValue($item, 'dc_rights', $metadata[22], $metadata[23]);
        $this->createValue($item, 'dc_source', $metadata[24]);
        $this->createValue($item, 'dc_subject', $metadata[25], $metadata[28], $metadata[31], $metadata[34],
            $metadata[37], $metadata[40], $metadata[43], $metadata[46], $metadata[49]);
    }

    /**
     * @throws Exception
     */
    public function import(string $dir, string $filename, array $metadata) : void {
        $m = [];
        preg_match('/^(.*?)_p/', $filename, $m);
        $identifier = $m[1];
        if ( ! array_key_exists($identifier, $metadata)) {
            throw new Exception("Unknown identifier: {$identifier}");
        }
        $meta = $metadata[$identifier];
        $compilationName = explode(',', $meta[0])[0];
        $compilation = $this->findCompilation($compilationName);

        $scrapbook = $this->findScrapbook($meta[1], $compilation, $meta);
        $this->createItem($scrapbook, $dir . '/' . $filename, $meta);
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
    public function setScrapbookRepository(ScrapbookRepository $scrapbookRepository) : void {
        $this->scrapbookRepository = $scrapbookRepository;
    }
}

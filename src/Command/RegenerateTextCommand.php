<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Entity\Folio;
use App\Repository\FolioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Soundasleep\Html2Text;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RegenerateTextCommand extends Command {
    private EntityManagerInterface $em;

    protected static $defaultName = 'app:regenerate-text';

    protected static $defaultDescription = 'Convert all HOCR to text';

    protected function configure() : void {
        $this->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int {
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $class = Folio::class;
        $q = $this->em->createQuery("SELECT folio FROM $class folio");
        $iterator = $q->iterate();
        foreach($iterator as $row) {
            $folio = $row[0];
            /** @var Folio $folio */
            $folio->setText(Html2Text::convert($folio->getHocr(), ['ignore_errors' => true, 'drop_links' => true]));
            $this->em->flush();
            $this->em->clear();
        }
        return 0;
    }

    /**
     * @required
     */
    public function setEntityManager(EntityManagerInterface $em) : void {
        $this->em = $em;
    }
}

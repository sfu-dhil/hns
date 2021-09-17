<?php

namespace App\Command;

use League\Csv\Reader;
use League\Csv\Statement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SplitCommand extends Command
{
    protected static $defaultName = 'app:split';
    protected static $defaultDescription = 'Parse a pagination file and generate the commands to split the PDFs listed in it';

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('file', InputArgument::REQUIRED, 'Pagination file')
            ->addArgument('dir', InputArgument::OPTIONAL, 'Location of the PDFs', '.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = $input->getArgument('file');
        $dir = $input->getArgument('dir');

        $csv = Reader::createFromPath($file);
        $csv->setHeaderOffset(0);

        foreach($csv as $record) {
            $range = preg_replace('/;\s*/', ',', $record['Page Range']);
            $marker = preg_replace('/;\s*/', 'p', $record['Page Range']);
            $part = basename($record['File'], '.pdf') . '_p' . $marker . '.pdf';
            $cmd = "qpdf ${record['File']} --pages . ${range} -- $part";
            $output->writeln($cmd);
        }

        return 0;
    }
}

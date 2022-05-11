<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractOrliwebImportCommand extends Command
{
    public function configure()
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Le fichier produit à importer');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $filename = $input->getArgument('file');

        $this->importer->import($filename);

        $io->success('Import lancé avec succès');

        return Command::SUCCESS;
    }
}

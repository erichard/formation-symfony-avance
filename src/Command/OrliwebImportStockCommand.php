<?php

declare(strict_types=1);

namespace App\Command;

use App\Orliweb\StockImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

#[AsCommand(
    name: 'app:orliweb:import-stock',
    description: 'Import le fichier de stock d\'orliweb',
)]
class OrliwebImportStockCommand extends Command
{
    private $orliwebStock;

    public function __construct(StockImporter $orliwebStock)
    {
        $this->orliwebStock = $orliwebStock;

        parent::__construct();
    }

    public function configure()
    {
        $this->addOption('file', 'f', InputOption::VALUE_REQUIRED, 'Le fichier stock à importer');
        $this->addOption('dir', 'd', InputOption::VALUE_REQUIRED, 'Un répertoire contenant des fichiers stock à importer');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (null !== $filename = $input->getOption('file')) {
            $this->orliwebStock->importFromFile($filename);
        } elseif (null !== $directory = $input->getOption('dir')) {
            $finder = new Finder();
            $files = $finder
                ->files()
                ->in($directory)
                ->name(['*.csv', '*.CSV'])
                ->depth('== 0')
                ->sortByName();

            foreach ($files as $file) {
                $this->orliwebStock->importFromFile($file->getRealPath());
            }
        }

        $io->success('Import lancé avec succès');

        return Command::SUCCESS;
    }
}

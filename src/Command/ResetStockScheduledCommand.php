<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\StockRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:reset-stock-scheduled'
)]
class ResetStockScheduledCommand extends Command
{
    public function __construct(private StockRepository $stockRepository)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->stockRepository->resetStockScheduled();

        return Command::SUCCESS;
    }
}

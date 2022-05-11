<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\StockSender;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:shop:update-stock',
    description: 'Update stock on all shops',
)]
class ShopUpdateStockCommand extends Command
{
    public function __construct(private StockSender $stockSender)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Show the requests but do not actually send them')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->stockSender->sendStockToShops();

        if ($input->getOption('dry-run')) {
        }

        return Command::SUCCESS;
    }
}

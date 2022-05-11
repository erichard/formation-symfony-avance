<?php

declare(strict_types=1);

namespace App\Command;

use App\Orliweb\TarifImporter;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:orliweb:import-tarifs',
    description: 'Import les tarifs d\'orliweb',
)]
class OrliwebImportTarifCommand extends AbstractOrliwebImportCommand
{
    public function __construct(TarifImporter $tarifImporter)
    {
        $this->importer = $tarifImporter;

        parent::__construct();
    }
}

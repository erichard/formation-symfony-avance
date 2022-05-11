<?php

declare(strict_types=1);

namespace App\Command;

use App\Orliweb\FabricationImporter;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:orliweb:import-fabrications',
    description: 'Import les fabrications d\'orliweb',
)]
class OrliwebImportFabricationCommand extends AbstractOrliwebImportCommand
{
    public function __construct(FabricationImporter $importer)
    {
        $this->importer = $importer;

        parent::__construct();
    }
}

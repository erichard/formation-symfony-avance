<?php

declare(strict_types=1);

namespace App\Command;

use App\Orliweb\LigneImporter;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:orliweb:import-lignes',
    description: 'Import les lignes d\'orliweb',
)]
class OrliwebImportLigneCommand extends AbstractOrliwebImportCommand
{
    public function __construct(LigneImporter $importer)
    {
        $this->importer = $importer;

        parent::__construct();
    }
}

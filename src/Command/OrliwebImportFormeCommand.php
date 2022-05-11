<?php

declare(strict_types=1);

namespace App\Command;

use App\Orliweb\FormeImporter;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:orliweb:import-formes',
    description: 'Import les formes d\'orliweb',
)]
class OrliwebImportFormeCommand extends AbstractOrliwebImportCommand
{
    public function __construct(FormeImporter $orliwebForme)
    {
        $this->importer = $orliwebForme;

        parent::__construct();
    }
}

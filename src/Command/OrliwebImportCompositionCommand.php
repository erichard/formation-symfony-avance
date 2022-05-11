<?php

declare(strict_types=1);

namespace App\Command;

use App\Orliweb\CompositionImporter;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:orliweb:import-compositions',
    description: 'Import les compositions d\'orliweb',
)]
class OrliwebImportCompositionCommand extends AbstractOrliwebImportCommand
{
    public function __construct(CompositionImporter $importer)
    {
        $this->importer = $importer;

        parent::__construct();
    }
}

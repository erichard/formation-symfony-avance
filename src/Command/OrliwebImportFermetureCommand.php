<?php

declare(strict_types=1);

namespace App\Command;

use App\Orliweb\FermetureImporter;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:orliweb:import-fermetures',
    description: 'Import les fermetures d\'orliweb',
)]
class OrliwebImportFermetureCommand extends AbstractOrliwebImportCommand
{
    public function __construct(FermetureImporter $importer)
    {
        $this->importer = $importer;

        parent::__construct();
    }
}

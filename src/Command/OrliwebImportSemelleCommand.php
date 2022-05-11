<?php

declare(strict_types=1);

namespace App\Command;

use App\Orliweb\SemelleImporter;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:orliweb:import-semelles',
    description: 'Import les semelles d\'orliweb',
)]
class OrliwebImportSemelleCommand extends AbstractOrliwebImportCommand
{
    public function __construct(SemelleImporter $importer)
    {
        $this->importer = $importer;

        parent::__construct();
    }
}

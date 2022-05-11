<?php

declare(strict_types=1);

namespace App\Command;

use App\Orliweb\GenreImporter;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:orliweb:import-genres',
    description: 'Import les genres d\'orliweb',
)]
class OrliwebImportGenreCommand extends AbstractOrliwebImportCommand
{
    public function __construct(GenreImporter $importer)
    {
        $this->importer = $importer;

        parent::__construct();
    }
}

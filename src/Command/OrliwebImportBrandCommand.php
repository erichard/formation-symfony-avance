<?php

declare(strict_types=1);

namespace App\Command;

use App\Orliweb\BrandImporter;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:orliweb:import-marques',
    description: 'Import les marques d\'orliweb',
)]
class OrliwebImportBrandCommand extends AbstractOrliwebImportCommand
{
    public function __construct(BrandImporter $orliwebBrand)
    {
        $this->importer = $orliwebBrand;

        parent::__construct();
    }
}

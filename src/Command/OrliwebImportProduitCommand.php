<?php

declare(strict_types=1);

namespace App\Command;

use App\Orliweb\ProductImporter;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:orliweb:import-produits',
    description: 'Import la base produit d\'orliweb',
)]
class OrliwebImportProduitCommand extends AbstractOrliwebImportCommand
{
    public function __construct(ProductImporter $importer)
    {
        $this->importer = $importer;

        parent::__construct();
    }
}

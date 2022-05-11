<?php

declare(strict_types=1);

namespace App\Command;

use App\Orliweb\ArticleImporter;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:orliweb:import-articles',
    description: 'Import la base article d\'orliweb',
)]
class OrliwebImportArticleCommand extends AbstractOrliwebImportCommand
{
    public function __construct(ArticleImporter $articleImporter)
    {
        $this->importer = $articleImporter;

        parent::__construct();
    }
}

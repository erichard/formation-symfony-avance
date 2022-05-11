<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220416133316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TRIGGER stock_bdt
            BEFORE DELETE ON stock
            FOR EACH ROW
            EXECUTE PROCEDURE stock_aiut()');

    }

    public function down(Schema $schema): void
    {
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220407142357 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('UPDATE "order" SET created_at = now() WHERE created_at IS NULL');
        $this->addSql('ALTER TABLE "order" ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE shop ADD enabled BOOLEAN DEFAULT false');
        $this->addSql("INSERT INTO shop (id, name, domain) SELECT nextval('shop_id_seq'), '[PROD] ' || name as name, domain from shop");
        $this->addSql("UPDATE shop SET name = '[PREPROD] ' || name WHERE name NOT LIKE '[PROD]%'");
        $this->addSql("UPDATE shop SET enabled = 't' WHERE name LIKE '[PREPROD]%'");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "order" ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE shop DROP enabled');
    }
}

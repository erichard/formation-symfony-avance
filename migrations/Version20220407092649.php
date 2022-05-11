<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220407092649 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "order" (id UUID NOT NULL, total_quantity INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN "order".id IS \'(DC2Type:ulid)\'');
        $this->addSql('CREATE TABLE order_item (id UUID NOT NULL, order_id UUID NOT NULL, product_size_id VARCHAR(13) NOT NULL, warehouse_id INT NOT NULL, quantity INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_52EA1F098D9F6D38 ON order_item (order_id)');
        $this->addSql('CREATE INDEX IDX_52EA1F099854B397 ON order_item (product_size_id)');
        $this->addSql('CREATE INDEX IDX_52EA1F095080ECDE ON order_item (warehouse_id)');
        $this->addSql('COMMENT ON COLUMN order_item.id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN order_item.order_id IS \'(DC2Type:ulid)\'');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES "order" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F099854B397 FOREIGN KEY (product_size_id) REFERENCES product_size (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F095080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE order_item DROP CONSTRAINT FK_52EA1F098D9F6D38');
        $this->addSql('DROP TABLE "order"');
        $this->addSql('DROP TABLE order_item');
    }
}

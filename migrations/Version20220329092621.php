<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220329092621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE shop_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE shop (id INT NOT NULL, name VARCHAR(255) NOT NULL, domain VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE shop_brand (shop_id INT NOT NULL, brand_id VARCHAR(3) NOT NULL, PRIMARY KEY(shop_id, brand_id))');
        $this->addSql('CREATE INDEX IDX_DB44A30A4D16C4DD ON shop_brand (shop_id)');
        $this->addSql('CREATE INDEX IDX_DB44A30A44F5D008 ON shop_brand (brand_id)');
        $this->addSql('ALTER TABLE shop_brand ADD CONSTRAINT FK_DB44A30A4D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shop_brand ADD CONSTRAINT FK_DB44A30A44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('INSERT INTO shop (id, name, domain) VALUES (nextval(\'shop_id_seq\'), ?,?)', ['Robeez', 'https://www.robeez.eu']);
        $this->addSql('INSERT INTO shop (id, name, domain) VALUES (nextval(\'shop_id_seq\'), ?,?)', ['Kickers', 'https://www.kickers.com']);
        $this->addSql('INSERT INTO shop (id, name, domain) VALUES (nextval(\'shop_id_seq\'), ?,?)', ['Stephane Kelian', 'https://www.stephanekelian.com']);
        $this->addSql('INSERT INTO shop (id, name, domain) VALUES (nextval(\'shop_id_seq\'), ?,?)', ['Piola', 'https://www.piola.fr']);
        $this->addSql('INSERT INTO shop (id, name, domain) VALUES (nextval(\'shop_id_seq\'), ?,?)', ['Catfootwear', 'https://www.catfootwear.fr']);
        $this->addSql('INSERT INTO shop (id, name, domain) VALUES (nextval(\'shop_id_seq\'), ?,?)', ['Hungaria Sport', 'https://www.hungariasport.com']);
        $this->addSql('INSERT INTO shop (id, name, domain) VALUES (nextval(\'shop_id_seq\'), ?,?)', ['Umbro', 'https://www.umbro.fr']);
        $this->addSql('INSERT INTO shop (id, name, domain) VALUES (nextval(\'shop_id_seq\'), ?,?)', ['Kids', 'https://www.kids-and-co.com']);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE shop_brand DROP CONSTRAINT FK_DB44A30A4D16C4DD');
        $this->addSql('DROP SEQUENCE shop_id_seq CASCADE');
        $this->addSql('DROP TABLE shop');
        $this->addSql('DROP TABLE shop_brand');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220510151215 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fabrication (id VARCHAR(3) NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE fermeture (id VARCHAR(30) NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE forme (id VARCHAR(3) NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE genre (id VARCHAR(3) NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE ligne (id VARCHAR(30) NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE semelle (id VARCHAR(3) NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE article ADD fabrication VARCHAR(3) DEFAULT NULL');
        $this->addSql('ALTER TABLE article ADD semelles JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE article ADD min_prix_vente INT DEFAULT NULL');
        $this->addSql('ALTER TABLE article ADD min_prix_achat INT DEFAULT NULL');
        $this->addSql('ALTER TABLE article DROP type_fabrication');
        $this->addSql('ALTER TABLE article DROP nom_semelle');
        $this->addSql('ALTER TABLE article ALTER semelle TYPE VARCHAR(3)');
        $this->addSql('ALTER TABLE article ALTER semelle DROP DEFAULT');
        $this->addSql('ALTER TABLE article ALTER ligne TYPE VARCHAR(30)');
        $this->addSql('ALTER TABLE article ALTER genre TYPE VARCHAR(3)');
        $this->addSql('ALTER TABLE article ALTER genre DROP DEFAULT');
        $this->addSql('ALTER TABLE article RENAME COLUMN type_fermeture TO fermeture');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E669EBAEA6 FOREIGN KEY (forme) REFERENCES forme (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66AE0B6B7A FOREIGN KEY (fabrication) REFERENCES fabrication (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E6659827437 FOREIGN KEY (fermeture) REFERENCES fermeture (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66C0D2BCD4 FOREIGN KEY (semelle) REFERENCES semelle (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E6657F0DB83 FOREIGN KEY (ligne) REFERENCES ligne (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66835033F8 FOREIGN KEY (genre) REFERENCES genre (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_23A0E669EBAEA6 ON article (forme)');
        $this->addSql('CREATE INDEX IDX_23A0E66AE0B6B7A ON article (fabrication)');
        $this->addSql('CREATE INDEX IDX_23A0E6659827437 ON article (fermeture)');
        $this->addSql('CREATE INDEX IDX_23A0E66C0D2BCD4 ON article (semelle)');
        $this->addSql('CREATE INDEX IDX_23A0E6657F0DB83 ON article (ligne)');
        $this->addSql('CREATE INDEX IDX_23A0E66835033F8 ON article (genre)');
        $this->addSql('ALTER INDEX idx_d34a04ad1c52f958 RENAME TO IDX_23A0E661C52F958');
        $this->addSql('ALTER TABLE order_item DROP CONSTRAINT fk_52ea1f099854b397');
        $this->addSql('DROP INDEX idx_52ea1f099854b397');
        $this->addSql('ALTER TABLE order_item RENAME COLUMN product_size_id TO product_id');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F094584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_52EA1F094584665A ON order_item (product_id)');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT fk_7a2806cb4584665a');
        $this->addSql('DROP INDEX idx_7a2806cb4584665a');
        $this->addSql('ALTER TABLE product ADD prix_vente INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD prix_achat INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product RENAME COLUMN product_id TO article_id');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD7294869C FOREIGN KEY (article_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D34A04AD7294869C ON product (article_id)');
        $this->addSql('ALTER TABLE shop ALTER enabled SET NOT NULL');
        $this->addSql('ALTER TABLE messenger_messages ALTER queue_name TYPE VARCHAR(190)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE article DROP CONSTRAINT FK_23A0E66AE0B6B7A');
        $this->addSql('ALTER TABLE article DROP CONSTRAINT FK_23A0E6659827437');
        $this->addSql('ALTER TABLE article DROP CONSTRAINT FK_23A0E669EBAEA6');
        $this->addSql('ALTER TABLE article DROP CONSTRAINT FK_23A0E66835033F8');
        $this->addSql('ALTER TABLE article DROP CONSTRAINT FK_23A0E6657F0DB83');
        $this->addSql('ALTER TABLE article DROP CONSTRAINT FK_23A0E66C0D2BCD4');
        $this->addSql('DROP TABLE fabrication');
        $this->addSql('DROP TABLE fermeture');
        $this->addSql('DROP TABLE forme');
        $this->addSql('DROP TABLE genre');
        $this->addSql('DROP TABLE ligne');
        $this->addSql('DROP TABLE semelle');
        $this->addSql('ALTER TABLE shop ALTER enabled DROP NOT NULL');
        $this->addSql('ALTER TABLE messenger_messages ALTER queue_name TYPE VARCHAR(255)');
        $this->addSql('DROP INDEX IDX_23A0E669EBAEA6');
        $this->addSql('DROP INDEX IDX_23A0E66AE0B6B7A');
        $this->addSql('DROP INDEX IDX_23A0E6659827437');
        $this->addSql('DROP INDEX IDX_23A0E66C0D2BCD4');
        $this->addSql('DROP INDEX IDX_23A0E6657F0DB83');
        $this->addSql('DROP INDEX IDX_23A0E66835033F8');
        $this->addSql('ALTER TABLE article ADD nom_semelle VARCHAR(3) DEFAULT NULL');
        $this->addSql('ALTER TABLE article DROP semelles');
        $this->addSql('ALTER TABLE article DROP min_prix_vente');
        $this->addSql('ALTER TABLE article DROP min_prix_achat');
        $this->addSql('ALTER TABLE article ALTER semelle TYPE JSON');
        $this->addSql('ALTER TABLE article ALTER semelle DROP DEFAULT');
        $this->addSql('ALTER TABLE article ALTER semelle TYPE JSON');
        $this->addSql('ALTER TABLE article ALTER ligne TYPE VARCHAR(3)');
        $this->addSql('ALTER TABLE article ALTER genre TYPE INT');
        $this->addSql('ALTER TABLE article ALTER genre DROP DEFAULT');
        $this->addSql('ALTER TABLE article ALTER genre TYPE INT');
        $this->addSql('ALTER TABLE article RENAME COLUMN fabrication TO type_fabrication');
        $this->addSql('ALTER TABLE article RENAME COLUMN fermeture TO type_fermeture');
        $this->addSql('ALTER INDEX idx_23a0e661c52f958 RENAME TO idx_d34a04ad1c52f958');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04AD7294869C');
        $this->addSql('DROP INDEX IDX_D34A04AD7294869C');
        $this->addSql('ALTER TABLE product DROP prix_vente');
        $this->addSql('ALTER TABLE product DROP prix_achat');
        $this->addSql('ALTER TABLE product RENAME COLUMN article_id TO product_id');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT fk_7a2806cb4584665a FOREIGN KEY (product_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_7a2806cb4584665a ON product (product_id)');
        $this->addSql('ALTER TABLE order_item DROP CONSTRAINT FK_52EA1F094584665A');
        $this->addSql('DROP INDEX IDX_52EA1F094584665A');
        $this->addSql('ALTER TABLE order_item RENAME COLUMN product_id TO product_size_id');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT fk_52ea1f099854b397 FOREIGN KEY (product_size_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_52ea1f099854b397 ON order_item (product_size_id)');
    }
}

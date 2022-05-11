<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220510112719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product RENAME TO article');
        $this->addSql('ALTER TABLE product_size RENAME TO product');

        $this->addSql("
            CREATE OR REPLACE FUNCTION stock_aiut() RETURNS TRIGGER
            LANGUAGE plpgsql
            SET SCHEMA 'public'
            AS $$
            BEGIN
                UPDATE product p
                    SET quantity_in_stock = (SELECT sum(quantity_available) FROM stock s WHERE s.ean = NEW.ean)
                WHERE p.id = NEW.ean;

                UPDATE article a
                    SET quantity_in_stock = (SELECT sum(quantity_in_stock) FROM product p WHERE a.id = p.article_id)
                WHERE a.id = (SELECT article_id FROM product WHERE id = NEW.ean);
            RETURN NEW;
            END;
            $$;");


        $this->addSql("
            CREATE OR REPLACE FUNCTION product_aiudt() RETURNS TRIGGER
            LANGUAGE plpgsql
            SET SCHEMA 'public'
            AS $$
            BEGIN
                IF COALESCE(NEW.prix_vente, NEW.prix_achat) IS NOT NULL THEN
                    UPDATE article SET
                        min_prix_vente = (SELECT min(prix_vente) FROM product p WHERE p.article_id = NEW.article_id)
                        , min_prix_achat = (SELECT min(prix_achat) FROM product p WHERE p.article_id = NEW.article_id)
                    WHERE id = NEW.article_id;
                END IF;
            RETURN NEW;
            END;
            $$;");

        $this->addSql('CREATE TRIGGER produit_ait
            AFTER INSERT ON product
            FOR EACH ROW
            EXECUTE PROCEDURE product_aiudt()');

        $this->addSql('CREATE TRIGGER product_aut
            AFTER UPDATE ON product
            FOR EACH ROW
            EXECUTE PROCEDURE product_aiudt()');

        $this->addSql('CREATE TRIGGER product_adt
            AFTER DELETE ON product
            FOR EACH ROW
            EXECUTE PROCEDURE product_aiudt()');
        $this->addSql('TRUNCATE TABLE article CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product RENAME TO product_size');
        $this->addSql('ALTER TABLE article RENAME TO product');
    }
}

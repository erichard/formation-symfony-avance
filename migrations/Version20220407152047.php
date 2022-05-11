<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220407152047 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            CREATE OR REPLACE FUNCTION stock_biut() RETURNS TRIGGER
            LANGUAGE plpgsql
            SET SCHEMA 'public'
            AS $$
            DECLARE
            new_quantity_available int;
            BEGIN
                new_quantity_available := NEW.quantity_on_hand - NEW.quantity_scheduled;
                IF new_quantity_available <= 0 THEN
                    new_quantity_available := 0;
                END IF;

                NEW.quantity_available := new_quantity_available;
            RETURN NEW;
            END;
            $$;");

        $this->addSql("
            CREATE OR REPLACE FUNCTION stock_aiut() RETURNS TRIGGER
            LANGUAGE plpgsql
            SET SCHEMA 'public'
            AS $$
            BEGIN
                UPDATE product_size ps
                    SET quantity_in_stock = (SELECT sum(quantity_available) FROM stock s WHERE s.ean = NEW.ean)
                WHERE ps.id = NEW.ean;

                UPDATE product p
                    SET quantity_in_stock = (SELECT sum(quantity_in_stock) FROM product_size ps WHERE p.id = ps.product_id)
                WHERE p.id = (SELECT product_id FROM product_size WHERE id = NEW.ean);
            RETURN NEW;
            END;
            $$;");

        $this->addSql('CREATE TRIGGER stock_but
            BEFORE UPDATE ON stock
            FOR EACH ROW
            WHEN (OLD.* IS DISTINCT FROM NEW.*)
            EXECUTE PROCEDURE stock_biut()');

        $this->addSql('CREATE TRIGGER stock_bit
            BEFORE INSERT ON stock
            FOR EACH ROW
            EXECUTE PROCEDURE stock_biut()');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE shop ALTER enabled SET DEFAULT false');
        $this->addSql('ALTER TABLE shop ALTER enabled DROP NOT NULL');
    }
}

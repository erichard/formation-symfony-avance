<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220404080132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock RENAME quantity TO quantity_on_hand');
        $this->addSql('ALTER TABLE stock ALTER quantity_on_hand SET DEFAULT 0');
        $this->addSql('ALTER TABLE stock ADD quantity_scheduled INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE stock ADD quantity_available INT DEFAULT 0 NOT NULL');
        $this->addSql('UPDATE stock SET quantity_available = quantity_on_hand - quantity_scheduled');

        $this->addSql("
            CREATE FUNCTION stock_aiut() RETURNS TRIGGER
            LANGUAGE plpgsql
            SET SCHEMA 'public'
            AS $$
            DECLARE
            new_quantity_available int;
            BEGIN
                UPDATE stock
                    SET quantity_available = (NEW.quantity_on_hand > NEW.quantity_scheduled ? NEW.quantity_on_hand - NEW.quantity_scheduled : 0)
                    WHERE ean = NEW.ean AND warehouse_id = NEW.warehouse_id
                        RETURNING quantity_available INTO new_quantity_available;

                UPDATE product_size ps
                    SET quantity_in_stock = (SELECT sum(quantity_available) FROM stock s WHERE s.ean = NEW.ean)
                WHERE ps.id = NEW.ean;

                UPDATE product p
                    SET quantity_in_stock = (SELECT sum(quantity_in_stock) FROM product_size ps WHERE p.id = ps.product_id)
                WHERE p.id = (SELECT product_id FROM product_size WHERE id = NEW.ean);

            RETURN NEW;
            END;
            $$;");

        $this->addSql('CREATE TRIGGER stock_aut
            AFTER UPDATE ON stock
            FOR EACH ROW
            WHEN (OLD.* IS DISTINCT FROM NEW.*)
            EXECUTE PROCEDURE stock_aiut()');

        $this->addSql('CREATE TRIGGER stock_ait
            AFTER INSERT ON stock
            FOR EACH ROW
            EXECUTE PROCEDURE stock_aiut()');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE stock ADD quantity INT NOT NULL');
        $this->addSql('ALTER TABLE stock DROP quantity_on_hand');
        $this->addSql('ALTER TABLE stock DROP quantity_scheduled');
        $this->addSql('ALTER TABLE stock DROP quantity_available');
        $this->addSql('DROP TRIGGER after_stock_changed ON stock');
        $this->addSql('DROP PROCEDURE update_stock');
    }
}

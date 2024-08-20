<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240820123825 extends AbstractMigration
{
    public function getDescription(): string
    {
        return "Basic entities and repositories";
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE award (
          id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
          awarded_to_id INTEGER NOT NULL,
          product_id INTEGER NOT NULL,
          created_at DATETIME NOT NULL,
          date_from DATETIME NOT NULL,
          date_to DATETIME NOT NULL,
          is_campaign BOOLEAN NOT NULL,
          total DOUBLE PRECISION NOT NULL,
          CONSTRAINT FK_9F987F7A29720A4 FOREIGN KEY (awarded_to_id) REFERENCES salesman (id) NOT DEFERRABLE INITIALLY IMMEDIATE,
          CONSTRAINT FK_9F987F7A4584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        )');
        $this->addSql('CREATE INDEX IDX_9F987F7A29720A4 ON award (awarded_to_id)');
        $this->addSql('CREATE INDEX IDX_9F987F7A4584665A ON award (product_id)');
        $this->addSql('CREATE TABLE campaign (
          id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
          product_id INTEGER NOT NULL,
          created_at DATETIME NOT NULL,
          active BOOLEAN NOT NULL,
          CONSTRAINT FK_1F1512DD4584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        )');
        $this->addSql('CREATE INDEX IDX_1F1512DD4584665A ON campaign (product_id)');
        $this->addSql('CREATE TABLE product (
          id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
          name VARCHAR(255) NOT NULL,
          unit_price DOUBLE PRECISION NOT NULL
        )');
        $this->addSql('CREATE TABLE sale (
          id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
          salesman_id INTEGER NOT NULL,
          sales_date DATETIME NOT NULL,
          total DOUBLE PRECISION DEFAULT NULL,
          CONSTRAINT FK_E54BC0059F7F22E2 FOREIGN KEY (salesman_id) REFERENCES salesman (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        )');
        $this->addSql('CREATE INDEX IDX_E54BC0059F7F22E2 ON sale (salesman_id)');
        $this->addSql('CREATE TABLE sale_product (
          sale_id INTEGER NOT NULL,
          product_id INTEGER NOT NULL,
          CONSTRAINT FK_A654C63F4A7E4868 FOREIGN KEY (sale_id) REFERENCES sale (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE,
          CONSTRAINT FK_A654C63F4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        )');
        $this->addSql('CREATE INDEX IDX_A654C63F4A7E4868 ON sale_product (sale_id)');
        $this->addSql('CREATE INDEX IDX_A654C63F4584665A ON sale_product (product_id)');
        $this->addSql('CREATE TABLE salesman (
          id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
          name VARCHAR(255) NOT NULL,
          lastname VARCHAR(255) NOT NULL,
          active BOOLEAN NOT NULL
        )');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE award');
        $this->addSql('DROP TABLE campaign');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE sale');
        $this->addSql('DROP TABLE sale_product');
        $this->addSql('DROP TABLE salesman');
    }
}

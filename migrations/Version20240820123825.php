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
        return "Entities and repositories";
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
          CONSTRAINT FK_8A5B2EE729720A4 FOREIGN KEY (awarded_to_id) REFERENCES salesman (id) NOT DEFERRABLE INITIALLY IMMEDIATE,
          CONSTRAINT FK_8A5B2EE74584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        )');
        $this->addSql('CREATE INDEX IDX_8A5B2EE729720A4 ON award (awarded_to_id)');
        $this->addSql('CREATE INDEX IDX_8A5B2EE74584665A ON award (product_id)');
        $this->addSql('CREATE TABLE award_amount (
          id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
          campaign BOOLEAN NOT NULL, amount DOUBLE PRECISION NOT NULL
        )');
        $this->addSql('CREATE TABLE bonus (
          id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
          salesman_id INTEGER NOT NULL,
          sale_comission_id INTEGER DEFAULT NULL,
          best_salesman_month_id INTEGER DEFAULT NULL,
          created_at DATETIME NOT NULL,
          date_from DATETIME NOT NULL,
          date_to DATETIME NOT NULL,
          product_commissions_total DOUBLE PRECISION DEFAULT NULL,
          campaign_awards_total DOUBLE PRECISION DEFAULT NULL,
          total DOUBLE PRECISION DEFAULT NULL,
          CONSTRAINT FK_9F987F7A9F7F22E2 FOREIGN KEY (salesman_id) REFERENCES salesman (id) NOT DEFERRABLE INITIALLY IMMEDIATE,
          CONSTRAINT FK_9F987F7A89A18DBB FOREIGN KEY (sale_comission_id) REFERENCES commission (id) NOT DEFERRABLE INITIALLY IMMEDIATE,
          CONSTRAINT FK_9F987F7A3BFE7C41 FOREIGN KEY (best_salesman_month_id) REFERENCES award (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        )');
        $this->addSql('CREATE INDEX IDX_9F987F7A9F7F22E2 ON bonus (salesman_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F987F7A89A18DBB ON bonus (sale_comission_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F987F7A3BFE7C41 ON bonus (best_salesman_month_id)');
        $this->addSql('CREATE TABLE bonus_product_commission (
          bonus_id INTEGER NOT NULL,
          product_commission_id INTEGER NOT NULL,
          PRIMARY KEY(bonus_id, product_commission_id),
          CONSTRAINT FK_6AF53A8469545666 FOREIGN KEY (bonus_id) REFERENCES bonus (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE,
          CONSTRAINT FK_6AF53A84FAC00240 FOREIGN KEY (product_commission_id) REFERENCES commission (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        )');
        $this->addSql('CREATE INDEX IDX_6AF53A8469545666 ON bonus_product_commission (bonus_id)');
        $this->addSql('CREATE INDEX IDX_6AF53A84FAC00240 ON bonus_product_commission (product_commission_id)');
        $this->addSql('CREATE TABLE bonus_award (
          bonus_id INTEGER NOT NULL,
          award_id INTEGER NOT NULL,
          PRIMARY KEY(bonus_id, award_id),
          CONSTRAINT FK_5FE3AD4469545666 FOREIGN KEY (bonus_id) REFERENCES bonus (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE,
          CONSTRAINT FK_5FE3AD443D5282CF FOREIGN KEY (award_id) REFERENCES award (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        )');
        $this->addSql('CREATE INDEX IDX_5FE3AD4469545666 ON bonus_award (bonus_id)');
        $this->addSql('CREATE INDEX IDX_5FE3AD443D5282CF ON bonus_award (award_id)');
        $this->addSql('CREATE TABLE campaign (
          id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
          product_id INTEGER NOT NULL,
          created_at DATETIME NOT NULL,
          active BOOLEAN NOT NULL,
          CONSTRAINT FK_1F1512DD4584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        )');
        $this->addSql('CREATE INDEX IDX_1F1512DD4584665A ON campaign (product_id)');
        $this->addSql('CREATE TABLE commission (
          id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
          salesman_id INTEGER NOT NULL,
          product_id INTEGER DEFAULT NULL,
          created_at DATETIME NOT NULL,
          from_date DATETIME NOT NULL,
          to_date DATETIME NOT NULL,
          units INTEGER NOT NULL,
          total DOUBLE PRECISION NOT NULL,
          type VARCHAR(255) NOT NULL,
          CONSTRAINT FK_1C6501589F7F22E2 FOREIGN KEY (salesman_id) REFERENCES salesman (id) NOT DEFERRABLE INITIALLY IMMEDIATE,
          CONSTRAINT FK_1C6501584584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        )');
        $this->addSql('CREATE INDEX IDX_1C6501589F7F22E2 ON commission (salesman_id)');
        $this->addSql('CREATE INDEX IDX_1C6501584584665A ON commission (product_id)');
        $this->addSql('CREATE TABLE sale_commission_sale (
          sale_commission_id INTEGER NOT NULL,
          sale_id INTEGER NOT NULL,
          PRIMARY KEY(sale_commission_id, sale_id),
          CONSTRAINT FK_9E13369D1AA9856 FOREIGN KEY (sale_commission_id) REFERENCES commission (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE,
          CONSTRAINT FK_9E13369D4A7E4868 FOREIGN KEY (sale_id) REFERENCES sale (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        )');
        $this->addSql('CREATE INDEX IDX_9E13369D1AA9856 ON sale_commission_sale (sale_commission_id)');
        $this->addSql('CREATE INDEX IDX_9E13369D4A7E4868 ON sale_commission_sale (sale_id)');
        $this->addSql('CREATE TABLE payroll (
          id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
          salesman_id INTEGER NOT NULL,
          bonuses_id INTEGER DEFAULT NULL,
          created_at DATETIME NOT NULL,
          date_from DATETIME NOT NULL,
          date_to DATETIME NOT NULL,
          base_salary DOUBLE PRECISION NOT NULL,
          total DOUBLE PRECISION NOT NULL,
          CONSTRAINT FK_499FBCC69F7F22E2 FOREIGN KEY (salesman_id) REFERENCES salesman (id) NOT DEFERRABLE INITIALLY IMMEDIATE,
          CONSTRAINT FK_499FBCC6F29D2B49 FOREIGN KEY (bonuses_id) REFERENCES bonus (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_499FBCC69F7F22E2 ON payroll (salesman_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_499FBCC6F29D2B49 ON payroll (bonuses_id)');
        $this->addSql('CREATE TABLE product (
          id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
          name VARCHAR(255) NOT NULL,
          unit_price DOUBLE PRECISION NOT NULL
        )');
        $this->addSql('CREATE TABLE product_commission_amount (
          id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
          product_id INTEGER NOT NULL,
          amount DOUBLE PRECISION DEFAULT NULL,
          CONSTRAINT FK_536CC1FD4584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_536CC1FD4584665A ON product_commission_amount (product_id)');
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
        $this->addSql('CREATE TABLE sale_commission_amount (
          id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
          min INTEGER NOT NULL, max INTEGER DEFAULT NULL,
          amount DOUBLE PRECISION NOT NULL
        )');
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
        $this->addSql('DROP TABLE award_amount');
        $this->addSql('DROP TABLE bonus');
        $this->addSql('DROP TABLE bonus_product_commission');
        $this->addSql('DROP TABLE bonus_award');
        $this->addSql('DROP TABLE campaign');
        $this->addSql('DROP TABLE commission');
        $this->addSql('DROP TABLE sale_commission_sale');
        $this->addSql('DROP TABLE payroll');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_commission_amount');
        $this->addSql('DROP TABLE sale');
        $this->addSql('DROP TABLE sale_product');
        $this->addSql('DROP TABLE sale_commission_amount');
        $this->addSql('DROP TABLE salesman');
    }
}

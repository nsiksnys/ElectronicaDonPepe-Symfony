<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240821192717 extends AbstractMigration
{
    public function getDescription(): string
    {
        return "Initial data";
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // Add to salesman table
        $this->addSql("INSERT INTO `salesman` (`id`, `active`, `lastname`, `name`) VALUES
            (1, 1, 'Diaz', 'Andrea'),
            (2, 1, 'Noble', 'Donna'),
            (3, 1, 'Dent', 'Arthur'),
            (4, 1, 'Perez', 'Juan')"
        );

        // Add to product table
        $this->addSql("INSERT INTO `product` (`id`, `name`, `unit_price`) VALUES
            (1, 'Doctor Who: Complete Series 4 (2008)', 13),
            (2, 'Doctor Who: Complete Specials (2009)', 11.25),
            (3, 'Doctor Who: Complete Series 5 (2010)', 12.5),
            (4, 'Doctor Who: Complete Series 6 (2011)', 13.75),
            (5, 'Doctor Who: Complete Series 7 (2012)', 33.94),
            (6, 'Doctor Who: The Ace Adventures (1987)', 10.99),
            (7, 'Wallander (Branagh): Series 3', 8),
            (8, 'Wallander (Branagh): Series 1 & 2 Box Set', 11.33),
            (9, 'Wallander (Lassgard): Original Films 1-6', 10),
            (10, 'Arne Dahl: Complete Series 1', 14.5),
            (11, 'Rachmaninov: The Ampico Piano Recordings', 9.99),
            (12, 'Turner in his Time', 18.2),
            (13, 'Inspector Morse: The Complete Series 1-12', 39.99),
            (14, 'Karajan: The Complete EMI Recordings Vol. 1', 109.01),
            (15, 'Karajan: The Complete EMI Recordings Vol. 2', 119.48)"
        );

        // Add to sale table
        $this->addSql("INSERT INTO `sale` (`id`, `sales_date`, `total`, `salesman_id`) VALUES
            (1, '2024-06-26 05:58:30', 137.68, 1),
            (2, '2024-07-17 19:43:27', 109.01, 1),
            (3, '2023-03-08 23:55:49', 167.51, 3),
            (4, '2023-12-09 08:17:22', 109.01, 1),
            (5, '2024-06-09 03:20:45', 22.24, 1),
            (6, '2023-03-30 12:48:49', 73.97, 3),
            (7, '2023-03-09 14:14:37', 24.74, 3),
            (8, '2024-08-01 20:40:16', 166.79, 2),
            (9, '2024-02-05 09:23:11', 51.9, 3),
            (10, '2023-10-31 23:09:54', 32.5, 2),
            (11, '2023-11-24 15:45:14', 13, 3),
            (12, '2023-11-24 16:18:25', 23, 3),
            (13, '2023-11-24 16:50:44', 26.2, 1),
            (14, '2024-06-29 18:36:48', 22.99, 1),
            (15, '2024-06-29 18:39:39', 109.01, 1)"
        );

        // Add to sale_product table
        $this->addSql("INSERT INTO `sale_product` (`sale_id`, `product_id`) VALUES
            (10, 7),
            (5, 2),
            (8, 4),
            (7, 4),
            (6, 11),
            (3, 14),
            (3, 8),
            (9, 2),
            (9, 8),
            (3, 10),
            (5, 6),
            (9, 11),
            (3, 9),
            (9, 7),
            (1, 12),
            (9, 8),
            (10, 9),
            (8, 9),
            (8, 5),
            (3, 2),
            (3, 8),
            (6, 1),
            (6, 6),
            (6, 13),
            (1, 15),
            (2, 14),
            (7, 6),
            (8, 14),
            (10, 10),
            (4, 14),
            (11, 1),
            (12, 1),
            (12, 9),
            (13, 7),
            (13, 12),
            (14, 1),
            (14, 11),
            (15, 14)"
        );

        // Add to campaign table
        $this->addSql("INSERT INTO `campaign` (`id`, `active`, `created_at`, `product_id`) VALUES
            (1, 1, '2024-05-18 20:54:31', 7),
            (2, 1, '2024-05-18 20:54:38', 8),
            (3, 1, '2024-05-18 20:54:42', 9)"
        );

        $this->addSql("INSERT INTO `product_commission_amount` (`id`, `amount`, `product_id`) VALUES
            (1, 2.1, 5),
            (2, 1, 1),
            (3, 3.5, 13),
            (4, 6, 9),
            (5, 1.99, 15),
            (6, 4.3, 7)"
        );

        $this->addSql("INSERT INTO `sale_commission_amount` (`id`, `max`, `min`, `amount`) VALUES
            (1, 5, 1, 200),
            (2, 10, 6, 400),
            (3, 11, 15, 700),
            (4, 0, 15, 1000)"
        );

        $this->addSql("INSERT INTO `award_amount` (`id`, `campaign`, `amount`) VALUES
            (1, 1, 1000),
            (2, 0, 2000)"
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}

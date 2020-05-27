<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200527141012 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, year_published DATE NOT NULL, min_players INT DEFAULT 1 NOT NULL, max_players INT DEFAULT 1 NOT NULL, min_playtime TIME DEFAULT NULL, max_playtime TIME DEFAULT NULL, min_age INT DEFAULT NULL, description LONGTEXT DEFAULT NULL, price NUMERIC(6, 2) NOT NULL, msrp NUMERIC(6, 2) DEFAULT NULL, discount NUMERIC(3, 2) DEFAULT NULL, artists JSON DEFAULT NULL, names JSON DEFAULT NULL, publishers JSON DEFAULT NULL, rules_url VARCHAR(255) DEFAULT NULL, official_url VARCHAR(255) DEFAULT NULL, game_id VARCHAR(20) DEFAULT NULL, published TINYINT(1) DEFAULT \'1\' NOT NULL, weight_amount INT DEFAULT NULL, weight_units VARCHAR(10) DEFAULT NULL, size_height NUMERIC(7, 3) DEFAULT NULL, size_width NUMERIC(7, 3) DEFAULT NULL, size_depth NUMERIC(7, 3) DEFAULT NULL, size_units VARCHAR(10) DEFAULT NULL, primary_publisher VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE game');
    }
}

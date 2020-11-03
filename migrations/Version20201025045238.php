<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201025045238 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE feed_rating (id INT AUTO_INCREMENT NOT NULL, feed_id INT DEFAULT NULL, ip_address VARCHAR(255) NOT NULL, rating INT DEFAULT NULL, INDEX IDX_1AAB56D651A5BC03 (feed_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feeder (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, feed_url LONGTEXT DEFAULT NULL, last_sync DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, hidden TINYINT(1) DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feeds (id INT AUTO_INCREMENT NOT NULL, feeder_id INT DEFAULT NULL, feed_data JSON DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, feed_url LONGTEXT DEFAULT NULL, custom_url LONGTEXT DEFAULT NULL, image_url LONGTEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, hidden TINYINT(1) DEFAULT NULL, unique_id VARCHAR(255) DEFAULT NULL, INDEX IDX_5A29F52F1274E059 (feeder_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE feed_rating ADD CONSTRAINT FK_1AAB56D651A5BC03 FOREIGN KEY (feed_id) REFERENCES feeds (id)');
        $this->addSql('ALTER TABLE feeds ADD CONSTRAINT FK_5A29F52F1274E059 FOREIGN KEY (feeder_id) REFERENCES feeder (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE feeds DROP FOREIGN KEY FK_5A29F52F1274E059');
        $this->addSql('ALTER TABLE feed_rating DROP FOREIGN KEY FK_1AAB56D651A5BC03');
        $this->addSql('DROP TABLE feed_rating');
        $this->addSql('DROP TABLE feeder');
        $this->addSql('DROP TABLE feeds');
    }
}

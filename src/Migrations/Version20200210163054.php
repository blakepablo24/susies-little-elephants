<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200210163054 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE children (id INT AUTO_INCREMENT NOT NULL, family_id INT NOT NULL, name VARCHAR(45) NOT NULL, start_date VARCHAR(45) DEFAULT NULL, INDEX IDX_A197B1BAC35E566A (family_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE families (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(45) NOT NULL, mum VARCHAR(45) DEFAULT NULL, dad VARCHAR(45) DEFAULT NULL, guardian VARCHAR(45) DEFAULT NULL, UNIQUE INDEX UNIQ_995F3FCCA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE posts (id INT AUTO_INCREMENT NOT NULL, child_id INT NOT NULL, subject LONGTEXT DEFAULT NULL, content LONGTEXT DEFAULT NULL, date DATE NOT NULL, image_file_name VARCHAR(255) DEFAULT NULL, INDEX IDX_885DBAFADD62C21B (child_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, name VARCHAR(45) NOT NULL, last_name VARCHAR(45) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE children ADD CONSTRAINT FK_A197B1BAC35E566A FOREIGN KEY (family_id) REFERENCES families (id)');
        $this->addSql('ALTER TABLE families ADD CONSTRAINT FK_995F3FCCA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFADD62C21B FOREIGN KEY (child_id) REFERENCES children (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFADD62C21B');
        $this->addSql('ALTER TABLE children DROP FOREIGN KEY FK_A197B1BAC35E566A');
        $this->addSql('ALTER TABLE families DROP FOREIGN KEY FK_995F3FCCA76ED395');
        $this->addSql('DROP TABLE children');
        $this->addSql('DROP TABLE families');
        $this->addSql('DROP TABLE posts');
        $this->addSql('DROP TABLE users');
    }
}

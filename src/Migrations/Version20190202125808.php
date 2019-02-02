<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190202125808 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE links_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE statistics_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE links (id INT NOT NULL, url VARCHAR(255) NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, life_time INT DEFAULT NULL, short_url VARCHAR(255) NOT NULL, is_active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE statistics (id INT NOT NULL, link_id INT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, referer VARCHAR(255) NOT NULL, ip_address VARCHAR(15) NOT NULL, browser VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E2D38B22ADA40271 ON statistics (link_id)');
        $this->addSql('ALTER TABLE statistics ADD CONSTRAINT FK_E2D38B22ADA40271 FOREIGN KEY (link_id) REFERENCES links (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE statistics DROP CONSTRAINT FK_E2D38B22ADA40271');
        $this->addSql('DROP SEQUENCE links_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE statistics_id_seq CASCADE');
        $this->addSql('DROP TABLE links');
        $this->addSql('DROP TABLE statistics');
    }
}

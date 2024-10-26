<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241026095740 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE application (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, applicant_id INTEGER DEFAULT NULL, job_id INTEGER DEFAULT NULL, application_status_id INTEGER NOT NULL, curriculum_vitae VARCHAR(255) NOT NULL, letter_of_motivation VARCHAR(255) NOT NULL, CONSTRAINT FK_A45BDDC197139001 FOREIGN KEY (applicant_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A45BDDC1BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A45BDDC1EFF67ABC FOREIGN KEY (application_status_id) REFERENCES application_status (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_A45BDDC197139001 ON application (applicant_id)');
        $this->addSql('CREATE INDEX IDX_A45BDDC1BE04EA9 ON application (job_id)');
        $this->addSql('CREATE INDEX IDX_A45BDDC1EFF67ABC ON application (application_status_id)');
        $this->addSql('CREATE TABLE application_status (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, status VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE company (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, logo VARCHAR(255) DEFAULT NULL, header_image VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE TABLE feature (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE job (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description CLOB NOT NULL, fte INTEGER NOT NULL, header_image VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE TABLE job_feature (job_id INTEGER NOT NULL, feature_id INTEGER NOT NULL, PRIMARY KEY(job_id, feature_id), CONSTRAINT FK_8538C2FDBE04EA9 FOREIGN KEY (job_id) REFERENCES job (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_8538C2FD60E4B879 FOREIGN KEY (feature_id) REFERENCES feature (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_8538C2FDBE04EA9 ON job_feature (job_id)');
        $this->addSql('CREATE INDEX IDX_8538C2FD60E4B879 ON job_feature (feature_id)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE application');
        $this->addSql('DROP TABLE application_status');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE feature');
        $this->addSql('DROP TABLE job');
        $this->addSql('DROP TABLE job_feature');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

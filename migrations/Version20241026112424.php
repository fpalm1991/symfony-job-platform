<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241026112424 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application ADD COLUMN is_archived BOOLEAN NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__application AS SELECT id, applicant_id, job_id, application_status_id, curriculum_vitae, letter_of_motivation FROM application');
        $this->addSql('DROP TABLE application');
        $this->addSql('CREATE TABLE application (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, applicant_id INTEGER DEFAULT NULL, job_id INTEGER DEFAULT NULL, application_status_id INTEGER NOT NULL, curriculum_vitae VARCHAR(255) NOT NULL, letter_of_motivation VARCHAR(255) NOT NULL, CONSTRAINT FK_A45BDDC197139001 FOREIGN KEY (applicant_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A45BDDC1BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A45BDDC1EFF67ABC FOREIGN KEY (application_status_id) REFERENCES application_status (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO application (id, applicant_id, job_id, application_status_id, curriculum_vitae, letter_of_motivation) SELECT id, applicant_id, job_id, application_status_id, curriculum_vitae, letter_of_motivation FROM __temp__application');
        $this->addSql('DROP TABLE __temp__application');
        $this->addSql('CREATE INDEX IDX_A45BDDC197139001 ON application (applicant_id)');
        $this->addSql('CREATE INDEX IDX_A45BDDC1BE04EA9 ON application (job_id)');
        $this->addSql('CREATE INDEX IDX_A45BDDC1EFF67ABC ON application (application_status_id)');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250916090447 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, company_id_id INT NOT NULL, created_by_id_id INT NOT NULL, assigned_to_id_id INT DEFAULT NULL, ticket_status VARCHAR(20) NOT NULL, priority VARCHAR(20) NOT NULL, assigned_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', code_folder VARCHAR(64) DEFAULT NULL, subject VARCHAR(255) NOT NULL, question LONGTEXT NOT NULL, due_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_billable TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_97A0ADA338B53C32 (company_id_id), INDEX IDX_97A0ADA3555BB088 (created_by_id_id), INDEX IDX_97A0ADA3975A96E (assigned_to_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA338B53C32 FOREIGN KEY (company_id_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3555BB088 FOREIGN KEY (created_by_id_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3975A96E FOREIGN KEY (assigned_to_id_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA338B53C32');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3555BB088');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3975A96E');
        $this->addSql('DROP TABLE ticket');
    }
}

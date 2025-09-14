<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250903111156 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD company_id INT NOT NULL, ADD user_status VARCHAR(20) NOT NULL, ADD first_name VARCHAR(120) NOT NULL, ADD last_name VARCHAR(120) NOT NULL, ADD phone VARCHAR(50) DEFAULT NULL, ADD last_login_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD agree_terms TINYINT(1) NOT NULL, ADD channels JSON DEFAULT NULL, ADD note VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64938B53C32 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('CREATE INDEX IDX_8D93D64938B53C32 ON user (company_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D64938B53C32');
        $this->addSql('DROP INDEX IDX_8D93D64938B53C32 ON `user`');
        $this->addSql('ALTER TABLE `user` DROP company_id, DROP user_status, DROP first_name, DROP last_name, DROP phone, DROP last_login_at, DROP agree_terms, DROP channels, DROP note, DROP created_at, DROP updated_at');
    }
}

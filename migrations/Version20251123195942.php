<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251123195942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE appointement (id INT AUTO_INCREMENT NOT NULL, date_hour DATETIME NOT NULL, end_date_hour DATETIME NOT NULL, confirmed TINYINT(1) NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, total_duration INT NOT NULL, comment LONGTEXT DEFAULT NULL, status VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE appointement_service (appointement_id INT NOT NULL, service_id INT NOT NULL, INDEX IDX_EFF7DFEF1EBF5025 (appointement_id), INDEX IDX_EFF7DFEFED5CA9E6 (service_id), PRIMARY KEY (appointement_id, service_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE appointement_service ADD CONSTRAINT FK_EFF7DFEF1EBF5025 FOREIGN KEY (appointement_id) REFERENCES appointement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE appointement_service ADD CONSTRAINT FK_EFF7DFEFED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointement_service DROP FOREIGN KEY FK_EFF7DFEF1EBF5025');
        $this->addSql('ALTER TABLE appointement_service DROP FOREIGN KEY FK_EFF7DFEFED5CA9E6');
        $this->addSql('DROP TABLE appointement');
        $this->addSql('DROP TABLE appointement_service');
    }
}

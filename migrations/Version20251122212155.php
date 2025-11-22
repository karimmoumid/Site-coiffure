<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251122212155 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE appointement_service (appointement_id INT NOT NULL, service_id INT NOT NULL, INDEX IDX_EFF7DFEF1EBF5025 (appointement_id), INDEX IDX_EFF7DFEFED5CA9E6 (service_id), PRIMARY KEY (appointement_id, service_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, original_name VARCHAR(255) NOT NULL, service_id INT DEFAULT NULL, service_category_id INT DEFAULT NULL, INDEX IDX_C53D045FED5CA9E6 (service_id), INDEX IDX_C53D045FDEDCBB4E (service_category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, small_description LONGTEXT NOT NULL, complet_description LONGTEXT NOT NULL, price NUMERIC(10, 2) NOT NULL, time NUMERIC(10, 2) NOT NULL, service_category_id INT NOT NULL, main_image_id INT DEFAULT NULL, INDEX IDX_E19D9AD2DEDCBB4E (service_category_id), UNIQUE INDEX UNIQ_E19D9AD2E4873418 (main_image_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE appointement_service ADD CONSTRAINT FK_EFF7DFEF1EBF5025 FOREIGN KEY (appointement_id) REFERENCES appointement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE appointement_service ADD CONSTRAINT FK_EFF7DFEFED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FDEDCBB4E FOREIGN KEY (service_category_id) REFERENCES service_category (id)');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2DEDCBB4E FOREIGN KEY (service_category_id) REFERENCES service_category (id)');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2E4873418 FOREIGN KEY (main_image_id) REFERENCES image (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointement_service DROP FOREIGN KEY FK_EFF7DFEF1EBF5025');
        $this->addSql('ALTER TABLE appointement_service DROP FOREIGN KEY FK_EFF7DFEFED5CA9E6');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FED5CA9E6');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FDEDCBB4E');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD2DEDCBB4E');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD2E4873418');
        $this->addSql('DROP TABLE appointement_service');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE service');
    }
}

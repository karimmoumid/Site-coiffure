<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251119224008 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE appointement (id INT AUTO_INCREMENT NOT NULL, date_hour DATETIME NOT NULL, confirmed TINYINT(1) NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, time NUMERIC(10, 2) NOT NULL, comment LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, original_name VARCHAR(255) NOT NULL, service_id INT DEFAULT NULL, service_category_id INT DEFAULT NULL, INDEX IDX_C53D045FED5CA9E6 (service_id), INDEX IDX_C53D045FDEDCBB4E (service_category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, lot_number INT NOT NULL, dlc DATE NOT NULL, product_category_id INT NOT NULL, INDEX IDX_D34A04ADBE6903FD (product_category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE product_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, sÃmall_description LONGTEXT NOT NULL, complet_description LONGTEXT NOT NULL, image VARCHAR(255) NOT NULL, price NUMERIC(10, 2) NOT NULL, time NUMERIC(10, 2) NOT NULL, actif TINYINT(1) NOT NULL, service_category_id INT DEFAULT NULL, INDEX IDX_E19D9AD2DEDCBB4E (service_category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE service_appointement (service_id INT NOT NULL, appointement_id INT NOT NULL, INDEX IDX_6235E428ED5CA9E6 (service_id), INDEX IDX_6235E4281EBF5025 (appointement_id), PRIMARY KEY (service_id, appointement_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE service_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, small_description LONGTEXT NOT NULL, complet_description LONGTEXT NOT NULL, image LONGTEXT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, last_login_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FDEDCBB4E FOREIGN KEY (service_category_id) REFERENCES service_category (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADBE6903FD FOREIGN KEY (product_category_id) REFERENCES product_category (id)');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2DEDCBB4E FOREIGN KEY (service_category_id) REFERENCES service_category (id)');
        $this->addSql('ALTER TABLE service_appointement ADD CONSTRAINT FK_6235E428ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_appointement ADD CONSTRAINT FK_6235E4281EBF5025 FOREIGN KEY (appointement_id) REFERENCES appointement (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FED5CA9E6');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FDEDCBB4E');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADBE6903FD');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD2DEDCBB4E');
        $this->addSql('ALTER TABLE service_appointement DROP FOREIGN KEY FK_6235E428ED5CA9E6');
        $this->addSql('ALTER TABLE service_appointement DROP FOREIGN KEY FK_6235E4281EBF5025');
        $this->addSql('DROP TABLE appointement');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_category');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE service_appointement');
        $this->addSql('DROP TABLE service_category');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

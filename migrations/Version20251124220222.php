<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251124220222 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY `FK_E19D9AD2E4873418`');
        $this->addSql('DROP INDEX UNIQ_E19D9AD2E4873418 ON service');
        $this->addSql('ALTER TABLE service DROP main_image_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service ADD main_image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT `FK_E19D9AD2E4873418` FOREIGN KEY (main_image_id) REFERENCES image (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E19D9AD2E4873418 ON service (main_image_id)');
    }
}

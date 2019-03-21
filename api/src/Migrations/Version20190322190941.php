<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190322190941 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE fos_user DROP COLUMN id');
        $this->addSql('ALTER TABLE fos_user RENAME COLUMN uuid TO id');

        $this->addSql('ALTER TABLE greeting DROP COLUMN id');
        $this->addSql('ALTER TABLE greeting RENAME COLUMN uuid TO id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE fos_user RENAME COLUMN id TO uuid');
        $this->addSql('ALTER TABLE fos_user ADD COLUMN id INTEGER');

        $this->addSql('ALTER TABLE greeting RENAME COLUMN id TO uuid');
        $this->addSql('ALTER TABLE greeting ADD COLUMN id INTEGER');
    }
}

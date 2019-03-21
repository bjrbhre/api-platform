<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190322184443 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX uniq_957a6479d17f50a6');
        $this->addSql('ALTER TABLE fos_user DROP CONSTRAINT fos_user_pkey');
        $this->addSql('ALTER TABLE fos_user ADD PRIMARY KEY (uuid)');
        $this->addSql('DROP INDEX uniq_46e3a4abd17f50a6');
        $this->addSql('ALTER TABLE greeting DROP CONSTRAINT greeting_pkey');
        $this->addSql('ALTER TABLE greeting ADD PRIMARY KEY (uuid)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE UNIQUE INDEX uniq_957a6479d17f50a6 ON fos_user (uuid)');
        $this->addSql('ALTER TABLE fos_user DROP CONSTRAINT fos_user_pkey');
        $this->addSql('ALTER TABLE fos_user ADD PRIMARY KEY (id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_46e3a4abd17f50a6 ON greeting (uuid)');
        $this->addSql('ALTER TABLE greeting DROP CONSTRAINT greeting_pkey');
        $this->addSql('ALTER TABLE greeting ADD PRIMARY KEY (id)');
    }
}

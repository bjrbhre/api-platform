<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190322165614 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE EXTENSION IF NOT EXISTS "uuid-ossp"');
        $this->addSql('ALTER TABLE fos_user ALTER uuid TYPE UUID');
        $this->addSql('ALTER TABLE fos_user ALTER COLUMN uuid SET DATA TYPE UUID USING (uuid_generate_v4())');
        $this->addSql('ALTER TABLE fos_user ALTER uuid SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN fos_user.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE greeting ALTER uuid TYPE UUID');
        $this->addSql('ALTER TABLE greeting ALTER COLUMN uuid SET DATA TYPE UUID USING (uuid_generate_v4())');
        $this->addSql('ALTER TABLE greeting ALTER uuid SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN greeting.uuid IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE fos_user ALTER uuid TYPE UUID');
        $this->addSql('ALTER TABLE fos_user ALTER uuid DROP DEFAULT');
        $this->addSql('ALTER TABLE fos_user ALTER uuid DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN fos_user.uuid IS NULL');
        $this->addSql('ALTER TABLE greeting ALTER uuid TYPE UUID');
        $this->addSql('ALTER TABLE greeting ALTER uuid DROP DEFAULT');
        $this->addSql('ALTER TABLE greeting ALTER uuid DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN greeting.uuid IS NULL');
    }
}

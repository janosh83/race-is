<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200517100059 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE visit ADD race_id INT NOT NULL');
        $this->addSql('ALTER TABLE visit ADD CONSTRAINT FK_437EE9396E59D40D FOREIGN KEY (race_id) REFERENCES race (id)');
        $this->addSql('CREATE INDEX IDX_437EE9396E59D40D ON visit (race_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE visit DROP FOREIGN KEY FK_437EE9396E59D40D');
        $this->addSql('DROP INDEX IDX_437EE9396E59D40D ON visit');
        $this->addSql('ALTER TABLE visit DROP race_id');
    }
}

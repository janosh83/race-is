<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200516091611 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE visit (id INT AUTO_INCREMENT NOT NULL, peak_id INT NOT NULL, team_id INT NOT NULL, time DATETIME NOT NULL, note LONGTEXT DEFAULT NULL, INDEX IDX_437EE939F848A361 (peak_id), INDEX IDX_437EE939296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE visit ADD CONSTRAINT FK_437EE939F848A361 FOREIGN KEY (peak_id) REFERENCES peak (id)');
        $this->addSql('ALTER TABLE visit ADD CONSTRAINT FK_437EE939296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('DROP TABLE team_peak');
        $this->addSql('ALTER TABLE peak ADD visit VARCHAR(255) NOT NULL, CHANGE short_id short_id VARCHAR(32) NOT NULL, CHANGE title title VARCHAR(128) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE team_peak (team_id INT NOT NULL, peak_id INT NOT NULL, INDEX IDX_73006006F848A361 (peak_id), INDEX IDX_73006006296CD8AE (team_id), PRIMARY KEY(team_id, peak_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE team_peak ADD CONSTRAINT FK_73006006296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team_peak ADD CONSTRAINT FK_73006006F848A361 FOREIGN KEY (peak_id) REFERENCES peak (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE visit');
        $this->addSql('ALTER TABLE peak DROP visit, CHANGE short_id short_id VARCHAR(31) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE title title VARCHAR(121) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}

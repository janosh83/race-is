<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220403193439 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE answer (id INT AUTO_INCREMENT NOT NULL, task_id INT NOT NULL, team_id INT NOT NULL, race_id INT NOT NULL, time DATETIME NOT NULL, note LONGTEXT DEFAULT NULL, INDEX IDX_DADD4A258DB60186 (task_id), INDEX IDX_DADD4A25296CD8AE (team_id), INDEX IDX_DADD4A256E59D40D (race_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(32) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, visit_id INT DEFAULT NULL, answer_id INT DEFAULT NULL, post_id INT DEFAULT NULL, filename VARCHAR(255) NOT NULL, INDEX IDX_C53D045F75FA0FF2 (visit_id), INDEX IDX_C53D045FAA334807 (answer_id), INDEX IDX_C53D045F4B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE journal_post (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, race_id INT NOT NULL, team_id INT NOT NULL, title VARCHAR(64) NOT NULL, text LONGTEXT NOT NULL, date DATE NOT NULL, INDEX IDX_E2B25405F675F31B (author_id), INDEX IDX_E2B254056E59D40D (race_id), INDEX IDX_E2B25405296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE peak (id INT AUTO_INCREMENT NOT NULL, race_id INT DEFAULT NULL, short_id VARCHAR(32) NOT NULL, title VARCHAR(128) NOT NULL, description LONGTEXT DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, points_per_visit INT NOT NULL, INDEX IDX_A2E1947D6E59D40D (race_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE race (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(63) NOT NULL, description LONGTEXT DEFAULT NULL, start_showing_peaks DATETIME NOT NULL, start_logging_peaks DATETIME NOT NULL, logo_path VARCHAR(255) DEFAULT NULL, journal_enabled TINYINT(1) NOT NULL, tasks_enabled TINYINT(1) NOT NULL, stop_logging_peaks DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registration (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, team_id INT NOT NULL, race_id INT NOT NULL, INDEX IDX_62A8A7A712469DE2 (category_id), INDEX IDX_62A8A7A7296CD8AE (team_id), INDEX IDX_62A8A7A76E59D40D (race_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, race_id INT DEFAULT NULL, title VARCHAR(128) NOT NULL, description LONGTEXT DEFAULT NULL, points_per_answer INT NOT NULL, INDEX IDX_527EDB256E59D40D (race_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(63) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team_user (team_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_5C722232296CD8AE (team_id), INDEX IDX_5C722232A76ED395 (user_id), PRIMARY KEY(team_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, name VARCHAR(63) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE visit (id INT AUTO_INCREMENT NOT NULL, peak_id INT NOT NULL, team_id INT NOT NULL, race_id INT NOT NULL, time DATETIME NOT NULL, note LONGTEXT DEFAULT NULL, INDEX IDX_437EE939F848A361 (peak_id), INDEX IDX_437EE939296CD8AE (team_id), INDEX IDX_437EE9396E59D40D (race_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A258DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A25296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A256E59D40D FOREIGN KEY (race_id) REFERENCES race (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F75FA0FF2 FOREIGN KEY (visit_id) REFERENCES visit (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FAA334807 FOREIGN KEY (answer_id) REFERENCES answer (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F4B89032C FOREIGN KEY (post_id) REFERENCES journal_post (id)');
        $this->addSql('ALTER TABLE journal_post ADD CONSTRAINT FK_E2B25405F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE journal_post ADD CONSTRAINT FK_E2B254056E59D40D FOREIGN KEY (race_id) REFERENCES race (id)');
        $this->addSql('ALTER TABLE journal_post ADD CONSTRAINT FK_E2B25405296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE peak ADD CONSTRAINT FK_A2E1947D6E59D40D FOREIGN KEY (race_id) REFERENCES race (id)');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A712469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A7296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A76E59D40D FOREIGN KEY (race_id) REFERENCES race (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB256E59D40D FOREIGN KEY (race_id) REFERENCES race (id)');
        $this->addSql('ALTER TABLE team_user ADD CONSTRAINT FK_5C722232296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team_user ADD CONSTRAINT FK_5C722232A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE visit ADD CONSTRAINT FK_437EE939F848A361 FOREIGN KEY (peak_id) REFERENCES peak (id)');
        $this->addSql('ALTER TABLE visit ADD CONSTRAINT FK_437EE939296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE visit ADD CONSTRAINT FK_437EE9396E59D40D FOREIGN KEY (race_id) REFERENCES race (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FAA334807');
        $this->addSql('ALTER TABLE registration DROP FOREIGN KEY FK_62A8A7A712469DE2');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F4B89032C');
        $this->addSql('ALTER TABLE visit DROP FOREIGN KEY FK_437EE939F848A361');
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A256E59D40D');
        $this->addSql('ALTER TABLE journal_post DROP FOREIGN KEY FK_E2B254056E59D40D');
        $this->addSql('ALTER TABLE peak DROP FOREIGN KEY FK_A2E1947D6E59D40D');
        $this->addSql('ALTER TABLE registration DROP FOREIGN KEY FK_62A8A7A76E59D40D');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB256E59D40D');
        $this->addSql('ALTER TABLE visit DROP FOREIGN KEY FK_437EE9396E59D40D');
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A258DB60186');
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A25296CD8AE');
        $this->addSql('ALTER TABLE journal_post DROP FOREIGN KEY FK_E2B25405296CD8AE');
        $this->addSql('ALTER TABLE registration DROP FOREIGN KEY FK_62A8A7A7296CD8AE');
        $this->addSql('ALTER TABLE team_user DROP FOREIGN KEY FK_5C722232296CD8AE');
        $this->addSql('ALTER TABLE visit DROP FOREIGN KEY FK_437EE939296CD8AE');
        $this->addSql('ALTER TABLE journal_post DROP FOREIGN KEY FK_E2B25405F675F31B');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE team_user DROP FOREIGN KEY FK_5C722232A76ED395');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F75FA0FF2');
        $this->addSql('DROP TABLE answer');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE journal_post');
        $this->addSql('DROP TABLE peak');
        $this->addSql('DROP TABLE race');
        $this->addSql('DROP TABLE registration');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE team_user');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE visit');
    }
}

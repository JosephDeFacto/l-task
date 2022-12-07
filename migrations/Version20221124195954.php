<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221124195954 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE results DROP FOREIGN KEY FK_9FA3E4146E59D40D');
        $this->addSql('DROP INDEX IDX_9FA3E4146E59D40D ON results');
        $this->addSql('ALTER TABLE results DROP race_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE results ADD race_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE results ADD CONSTRAINT FK_9FA3E4146E59D40D FOREIGN KEY (race_id) REFERENCES race (id)');
        $this->addSql('CREATE INDEX IDX_9FA3E4146E59D40D ON results (race_id)');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221124200028 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE race ADD results_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE race ADD CONSTRAINT FK_DA6FBBAF8A30AB9 FOREIGN KEY (results_id) REFERENCES results (id)');
        $this->addSql('CREATE INDEX IDX_DA6FBBAF8A30AB9 ON race (results_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE race DROP FOREIGN KEY FK_DA6FBBAF8A30AB9');
        $this->addSql('DROP INDEX IDX_DA6FBBAF8A30AB9 ON race');
        $this->addSql('ALTER TABLE race DROP results_id');
    }
}

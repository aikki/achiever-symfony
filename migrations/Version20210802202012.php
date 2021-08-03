<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210802202012 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE user_milestone_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE user_milestone (id INT NOT NULL, achiever_id INT NOT NULL, milestone_id INT NOT NULL, is_achieved BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4F4C0E66E503DEDD ON user_milestone (achiever_id)');
        $this->addSql('CREATE INDEX IDX_4F4C0E664B3E2EDA ON user_milestone (milestone_id)');
        $this->addSql('ALTER TABLE user_milestone ADD CONSTRAINT FK_4F4C0E66E503DEDD FOREIGN KEY (achiever_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_milestone ADD CONSTRAINT FK_4F4C0E664B3E2EDA FOREIGN KEY (milestone_id) REFERENCES milestone (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE user_milestone_id_seq CASCADE');
        $this->addSql('DROP TABLE user_milestone');
    }
}

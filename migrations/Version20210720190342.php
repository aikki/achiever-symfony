<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210720190342 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE user_goal_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE user_goal (id INT NOT NULL, achiever_id INT NOT NULL, goal_id INT NOT NULL, is_achieved BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_865DA7E7E503DEDD ON user_goal (achiever_id)');
        $this->addSql('CREATE INDEX IDX_865DA7E7667D1AFE ON user_goal (goal_id)');
        $this->addSql('ALTER TABLE user_goal ADD CONSTRAINT FK_865DA7E7E503DEDD FOREIGN KEY (achiever_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_goal ADD CONSTRAINT FK_865DA7E7667D1AFE FOREIGN KEY (goal_id) REFERENCES goal (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE user_goal_id_seq CASCADE');
        $this->addSql('DROP TABLE user_goal');
    }
}

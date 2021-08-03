<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210802164707 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE milestone_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE milestone (id INT NOT NULL, club_id INT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, icon_class_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4FAC838261190A32 ON milestone (club_id)');
        $this->addSql('CREATE TABLE milestone_goal (milestone_id INT NOT NULL, goal_id INT NOT NULL, PRIMARY KEY(milestone_id, goal_id))');
        $this->addSql('CREATE INDEX IDX_32B096DC4B3E2EDA ON milestone_goal (milestone_id)');
        $this->addSql('CREATE INDEX IDX_32B096DC667D1AFE ON milestone_goal (goal_id)');
        $this->addSql('ALTER TABLE milestone ADD CONSTRAINT FK_4FAC838261190A32 FOREIGN KEY (club_id) REFERENCES club (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE milestone_goal ADD CONSTRAINT FK_32B096DC4B3E2EDA FOREIGN KEY (milestone_id) REFERENCES milestone (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE milestone_goal ADD CONSTRAINT FK_32B096DC667D1AFE FOREIGN KEY (goal_id) REFERENCES goal (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE milestone_goal DROP CONSTRAINT FK_32B096DC4B3E2EDA');
        $this->addSql('DROP SEQUENCE milestone_id_seq CASCADE');
        $this->addSql('DROP TABLE milestone');
        $this->addSql('DROP TABLE milestone_goal');
    }
}

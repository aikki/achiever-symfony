<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210611171935 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE user_club_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE user_club (id INT NOT NULL, member_id INT NOT NULL, club_id INT NOT NULL, join_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_owner BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C26F74BB7597D3FE ON user_club (member_id)');
        $this->addSql('CREATE INDEX IDX_C26F74BB61190A32 ON user_club (club_id)');
        $this->addSql('ALTER TABLE user_club ADD CONSTRAINT FK_C26F74BB7597D3FE FOREIGN KEY (member_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_club ADD CONSTRAINT FK_C26F74BB61190A32 FOREIGN KEY (club_id) REFERENCES club (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE user_club_id_seq CASCADE');
        $this->addSql('DROP TABLE user_club');
    }
}

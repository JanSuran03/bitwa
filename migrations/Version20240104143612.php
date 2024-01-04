<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240104143612 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__rooms AS SELECT id, group_id, building, name, is_public, is_locked FROM rooms');
        $this->addSql('DROP TABLE rooms');
        $this->addSql('CREATE TABLE rooms (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, group_id INTEGER DEFAULT NULL, building VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, is_public BOOLEAN NOT NULL, is_locked BOOLEAN NOT NULL, CONSTRAINT FK_7CA11A96FE54D947 FOREIGN KEY (group_id) REFERENCES groups (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO rooms (id, group_id, building, name, is_public, is_locked) SELECT id, group_id, building, name, is_public, is_locked FROM __temp__rooms');
        $this->addSql('DROP TABLE __temp__rooms');
        $this->addSql('CREATE INDEX IDX_7CA11A96FE54D947 ON rooms (group_id)');
        $this->addSql('CREATE UNIQUE INDEX room_name_unique_constraint ON rooms (building, name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__rooms AS SELECT id, group_id, building, name, is_public, is_locked FROM rooms');
        $this->addSql('DROP TABLE rooms');
        $this->addSql('CREATE TABLE rooms (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, group_id INTEGER DEFAULT NULL, building VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, is_public BOOLEAN NOT NULL, is_locked BOOLEAN NOT NULL, CONSTRAINT FK_7CA11A96FE54D947 FOREIGN KEY (group_id) REFERENCES groups (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO rooms (id, group_id, building, name, is_public, is_locked) SELECT id, group_id, building, name, is_public, is_locked FROM __temp__rooms');
        $this->addSql('DROP TABLE __temp__rooms');
        $this->addSql('CREATE INDEX IDX_7CA11A96FE54D947 ON rooms (group_id)');
    }
}

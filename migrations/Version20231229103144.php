<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231229103144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__rooms AS SELECT id, building, name, is_public, is_locked FROM rooms');
        $this->addSql('DROP TABLE rooms');
        $this->addSql('CREATE TABLE rooms (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, group_id INTEGER DEFAULT NULL, building VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, is_public BOOLEAN NOT NULL, is_locked BOOLEAN NOT NULL, CONSTRAINT FK_7CA11A96FE54D947 FOREIGN KEY (group_id) REFERENCES groups (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO rooms (id, building, name, is_public, is_locked) SELECT id, building, name, is_public, is_locked FROM __temp__rooms');
        $this->addSql('DROP TABLE __temp__rooms');
        $this->addSql('CREATE INDEX IDX_7CA11A96FE54D947 ON rooms (group_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__users AS SELECT id, name, email, password, roles FROM users');
        $this->addSql('DROP TABLE users');
        $this->addSql('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        )');
        $this->addSql('INSERT INTO users (id, name, email, password, roles) SELECT id, name, email, password, roles FROM __temp__users');
        $this->addSql('DROP TABLE __temp__users');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__rooms AS SELECT id, building, name, is_public, is_locked FROM rooms');
        $this->addSql('DROP TABLE rooms');
        $this->addSql('CREATE TABLE rooms (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, building VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, is_public BOOLEAN NOT NULL, is_locked BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO rooms (id, building, name, is_public, is_locked) SELECT id, building, name, is_public, is_locked FROM __temp__rooms');
        $this->addSql('DROP TABLE __temp__rooms');
        $this->addSql('CREATE TEMPORARY TABLE __temp__users AS SELECT id, name, email, password, roles FROM users');
        $this->addSql('DROP TABLE users');
        $this->addSql('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles CLOB DEFAULT \'"[]"\' NOT NULL --(DC2Type:json)
        )');
        $this->addSql('INSERT INTO users (id, name, email, password, roles) SELECT id, name, email, password, roles FROM __temp__users');
        $this->addSql('DROP TABLE __temp__users');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231226134130 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rooms ADD COLUMN is_public BOOLEAN NOT NULL DEFAULT false');
        $this->addSql('ALTER TABLE users ADD COLUMN password VARCHAR(255) NOT NULL DEFAULT password');
        $this->addSql('ALTER TABLE users ADD COLUMN is_admin BOOLEAN NOT NULL DEFAULT false');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__rooms AS SELECT id, building, number FROM rooms');
        $this->addSql('DROP TABLE rooms');
        $this->addSql('CREATE TABLE rooms (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, building VARCHAR(255) NOT NULL, number VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO rooms (id, building, number) SELECT id, building, number FROM __temp__rooms');
        $this->addSql('DROP TABLE __temp__rooms');
        $this->addSql('CREATE TEMPORARY TABLE __temp__users AS SELECT id, name, email FROM users');
        $this->addSql('DROP TABLE users');
        $this->addSql('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO users (id, name, email) SELECT id, name, email FROM __temp__users');
        $this->addSql('DROP TABLE __temp__users');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231230133656 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__reservations AS SELECT id, author_id, room_id, is_approved, time_from, time_to FROM reservations');
        $this->addSql('DROP TABLE reservations');
        $this->addSql('CREATE TABLE reservations (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER DEFAULT NULL, room_id INTEGER DEFAULT NULL, responsible_user_id INTEGER DEFAULT NULL, is_approved BOOLEAN NOT NULL, time_from DATETIME NOT NULL, time_to DATETIME NOT NULL, CONSTRAINT FK_4DA239F675F31B FOREIGN KEY (author_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_4DA23954177093 FOREIGN KEY (room_id) REFERENCES rooms (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_4DA239BDAD1998 FOREIGN KEY (responsible_user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO reservations (id, author_id, room_id, is_approved, time_from, time_to) SELECT id, author_id, room_id, is_approved, time_from, time_to FROM __temp__reservations');
        $this->addSql('DROP TABLE __temp__reservations');
        $this->addSql('CREATE INDEX IDX_4DA23954177093 ON reservations (room_id)');
        $this->addSql('CREATE INDEX IDX_4DA239F675F31B ON reservations (author_id)');
        $this->addSql('CREATE INDEX IDX_4DA239BDAD1998 ON reservations (responsible_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__reservations AS SELECT id, author_id, room_id, is_approved, time_from, time_to FROM reservations');
        $this->addSql('DROP TABLE reservations');
        $this->addSql('CREATE TABLE reservations (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER DEFAULT NULL, room_id INTEGER DEFAULT NULL, is_approved BOOLEAN NOT NULL, time_from DATETIME NOT NULL, time_to DATETIME NOT NULL, CONSTRAINT FK_4DA239F675F31B FOREIGN KEY (author_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_4DA23954177093 FOREIGN KEY (room_id) REFERENCES rooms (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO reservations (id, author_id, room_id, is_approved, time_from, time_to) SELECT id, author_id, room_id, is_approved, time_from, time_to FROM __temp__reservations');
        $this->addSql('DROP TABLE __temp__reservations');
        $this->addSql('CREATE INDEX IDX_4DA239F675F31B ON reservations (author_id)');
        $this->addSql('CREATE INDEX IDX_4DA23954177093 ON reservations (room_id)');
    }
}

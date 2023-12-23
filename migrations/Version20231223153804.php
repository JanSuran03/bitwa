<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231223153804 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE groups (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, parent_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, CONSTRAINT FK_F06D3970727ACA70 FOREIGN KEY (parent_id) REFERENCES groups (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_F06D3970727ACA70 ON groups (parent_id)');
        $this->addSql('CREATE TABLE reservations (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER DEFAULT NULL, is_approved BOOLEAN NOT NULL, time_from DATETIME NOT NULL, time_to DATETIME NOT NULL, room INTEGER NOT NULL, CONSTRAINT FK_4DA239F675F31B FOREIGN KEY (author_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_4DA239F675F31B ON reservations (author_id)');
        $this->addSql('CREATE TABLE reservation_user (reservation_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(reservation_id, user_id), CONSTRAINT FK_9BAA1B21B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9BAA1B21A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_9BAA1B21B83297E7 ON reservation_user (reservation_id)');
        $this->addSql('CREATE INDEX IDX_9BAA1B21A76ED395 ON reservation_user (user_id)');
        $this->addSql('CREATE TABLE rooms (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, building INTEGER NOT NULL, number INTEGER NOT NULL)');
        $this->addSql('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE group_members (user_id INTEGER NOT NULL, group_id INTEGER NOT NULL, PRIMARY KEY(user_id, group_id), CONSTRAINT FK_C3A086F3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C3A086F3FE54D947 FOREIGN KEY (group_id) REFERENCES groups (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_C3A086F3A76ED395 ON group_members (user_id)');
        $this->addSql('CREATE INDEX IDX_C3A086F3FE54D947 ON group_members (group_id)');
        $this->addSql('CREATE TABLE room_members (user_id INTEGER NOT NULL, room_id INTEGER NOT NULL, PRIMARY KEY(user_id, room_id), CONSTRAINT FK_A9826E1FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A9826E1F54177093 FOREIGN KEY (room_id) REFERENCES rooms (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_A9826E1FA76ED395 ON room_members (user_id)');
        $this->addSql('CREATE INDEX IDX_A9826E1F54177093 ON room_members (room_id)');
        $this->addSql('CREATE TABLE room_managers (user_id INTEGER NOT NULL, room_id INTEGER NOT NULL, PRIMARY KEY(user_id, room_id), CONSTRAINT FK_9AF20C2A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9AF20C254177093 FOREIGN KEY (room_id) REFERENCES rooms (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_9AF20C2A76ED395 ON room_managers (user_id)');
        $this->addSql('CREATE INDEX IDX_9AF20C254177093 ON room_managers (room_id)');
        $this->addSql('CREATE TABLE group_managers (user_id INTEGER NOT NULL, group_id INTEGER NOT NULL, PRIMARY KEY(user_id, group_id), CONSTRAINT FK_A079AC79A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A079AC79FE54D947 FOREIGN KEY (group_id) REFERENCES groups (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_A079AC79A76ED395 ON group_managers (user_id)');
        $this->addSql('CREATE INDEX IDX_A079AC79FE54D947 ON group_managers (group_id)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE groups');
        $this->addSql('DROP TABLE reservations');
        $this->addSql('DROP TABLE reservation_user');
        $this->addSql('DROP TABLE rooms');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE group_members');
        $this->addSql('DROP TABLE room_members');
        $this->addSql('DROP TABLE room_managers');
        $this->addSql('DROP TABLE group_managers');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

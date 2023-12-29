<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231229104108 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invitations (reservation_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(reservation_id, user_id), CONSTRAINT FK_232710AEB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_232710AEA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_232710AEB83297E7 ON invitations (reservation_id)');
        $this->addSql('CREATE INDEX IDX_232710AEA76ED395 ON invitations (user_id)');
        $this->addSql('DROP TABLE reservation_user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reservation_user (reservation_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(reservation_id, user_id), CONSTRAINT FK_9BAA1B21B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservations (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9BAA1B21A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_9BAA1B21A76ED395 ON reservation_user (user_id)');
        $this->addSql('CREATE INDEX IDX_9BAA1B21B83297E7 ON reservation_user (reservation_id)');
        $this->addSql('DROP TABLE invitations');
    }
}

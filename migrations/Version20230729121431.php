<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230729121431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ticket (id SERIAL NOT NULL, transaction_id INT DEFAULT NULL, moderator_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, comment VARCHAR(1000) DEFAULT NULL, status SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_97A0ADA32FC0CB0F ON ticket (transaction_id)');
        $this->addSql('CREATE INDEX ticket__transaction_id__index ON ticket (transaction_id)');
        $this->addSql('CREATE INDEX ticket__moderator_id__index ON ticket (moderator_id)');
        $this->addSql('COMMENT ON COLUMN ticket.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT ticket__transaction_id__index FOREIGN KEY (transaction_id) REFERENCES transaction (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT ticket__moderator_id__index FOREIGN KEY (moderator_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket DROP CONSTRAINT ticket__transaction_id__index');
        $this->addSql('ALTER TABLE ticket DROP CONSTRAINT ticket__moderator_id__index');
        $this->addSql('DROP TABLE ticket');
    }
}

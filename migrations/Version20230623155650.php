<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230623155650 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE currency ALTER rate TYPE NUMERIC(12, 6)');
        $this->addSql('ALTER TABLE product ALTER amount TYPE NUMERIC(10, 2)');
        $this->addSql('ALTER TABLE transaction ALTER amount TYPE NUMERIC(10, 2)');
        $this->addSql('ALTER TABLE "user" ALTER balance TYPE NUMERIC(10, 2)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE currency ALTER rate TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE product ALTER amount TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE "user" ALTER balance TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE transaction ALTER amount TYPE DOUBLE PRECISION');
    }
}

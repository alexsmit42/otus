<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230622144240 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function isTransactional(): bool
    {
        return false;
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS purchase__created_at__index ON purchase (created_at)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS purchase__status__index ON purchase (status)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS transaction__created_at__index ON transaction (created_at)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS transaction__status__index ON transaction (status)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS transaction__direction__index ON transaction (direction)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX purchase__created_at__index');
        $this->addSql('DROP INDEX purchase__status__index');
        $this->addSql('DROP INDEX transaction__created_at__index');
        $this->addSql('DROP INDEX transaction__status__index');
        $this->addSql('DROP INDEX transaction__direction__index');
    }
}

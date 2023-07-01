<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230625093819 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER INDEX uniq_5373c9665e237e06 RENAME TO uniq__country_name');
        $this->addSql('ALTER INDEX uniq_6956883f61587f41 RENAME TO uniq__currency_iso');
        $this->addSql('ALTER TABLE method ALTER min_limit TYPE NUMERIC(10, 2)');
        $this->addSql('ALTER TABLE method ALTER max_limit TYPE NUMERIC(10, 2)');
        $this->addSql('ALTER TABLE product ALTER currency_id DROP NOT NULL');
        $this->addSql('ALTER TABLE purchase ALTER buyer_id DROP NOT NULL');
        $this->addSql('ALTER TABLE transaction ALTER payer_id DROP NOT NULL');
        $this->addSql('ALTER TABLE transaction ALTER method_id DROP NOT NULL');
        $this->addSql('CREATE INDEX transaction__method_id__status__index ON transaction (method_id, status)');
        $this->addSql('ALTER INDEX uniq_8d93d649aa08cb10 RENAME TO uniq__user_login');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX transaction__method_id__status__index');
        $this->addSql('ALTER TABLE transaction ALTER payer_id SET NOT NULL');
        $this->addSql('ALTER TABLE transaction ALTER method_id SET NOT NULL');
        $this->addSql('ALTER TABLE product ALTER currency_id SET NOT NULL');
        $this->addSql('ALTER TABLE method ALTER min_limit TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE method ALTER max_limit TYPE DOUBLE PRECISION');
        $this->addSql('ALTER INDEX uniq__currency_iso RENAME TO uniq_6956883f61587f41');
        $this->addSql('ALTER INDEX uniq__country_name RENAME TO uniq_5373c9665e237e06');
        $this->addSql('ALTER INDEX uniq__user_login RENAME TO uniq_8d93d649aa08cb10');
        $this->addSql('ALTER TABLE purchase ALTER buyer_id SET NOT NULL');
    }
}

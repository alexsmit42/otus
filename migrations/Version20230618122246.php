<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230618122246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER INDEX uniq__country_name RENAME TO UNIQ_5373C9665E237E06');
        $this->addSql('ALTER INDEX uniq__currency_iso RENAME TO UNIQ_6956883F61587F41');
        $this->addSql('ALTER INDEX uniq__user_login RENAME TO UNIQ_8D93D649AA08CB10');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER INDEX uniq_6956883f61587f41 RENAME TO uniq__currency_iso');
        $this->addSql('ALTER INDEX uniq_5373c9665e237e06 RENAME TO uniq__country_name');
        $this->addSql('ALTER INDEX uniq_8d93d649aa08cb10 RENAME TO uniq__user_login');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230624142129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE method_country (method_id INT NOT NULL, country_id INT NOT NULL, PRIMARY KEY(method_id, country_id))');
        $this->addSql('CREATE INDEX IDX_57615A2919883967 ON method_country (method_id)');
        $this->addSql('CREATE INDEX IDX_57615A29F92F3E70 ON method_country (country_id)');
        $this->addSql('ALTER TABLE method_country ADD CONSTRAINT fk__method_country__method_id FOREIGN KEY (method_id) REFERENCES method (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE method_country ADD CONSTRAINT fk__method_country__country_id FOREIGN KEY (country_id) REFERENCES country (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE country_method DROP CONSTRAINT fk__country_method__country_id');
        $this->addSql('ALTER TABLE country_method DROP CONSTRAINT fk__country_method__method_id');
        $this->addSql('DROP TABLE country_method');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE country_method (country_id INT NOT NULL, method_id INT NOT NULL, PRIMARY KEY(country_id, method_id))');
        $this->addSql('CREATE INDEX idx_1e21d11319883967 ON country_method (method_id)');
        $this->addSql('CREATE INDEX idx_1e21d113f92f3e70 ON country_method (country_id)');
        $this->addSql('ALTER TABLE country_method ADD CONSTRAINT fk__country_method__country_id FOREIGN KEY (country_id) REFERENCES country (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE country_method ADD CONSTRAINT fk__country_method__method_id FOREIGN KEY (method_id) REFERENCES method (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE method_country DROP CONSTRAINT fk__method_country__method_id');
        $this->addSql('ALTER TABLE method_country DROP CONSTRAINT fk__method_country__country_id');
        $this->addSql('DROP TABLE method_country');
    }
}

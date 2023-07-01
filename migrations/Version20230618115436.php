<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230618115436 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE country (id SERIAL NOT NULL, name VARCHAR(40) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq__country_name ON country (name)');
        $this->addSql('CREATE TABLE country_method (country_id INT NOT NULL, method_id INT NOT NULL, PRIMARY KEY(country_id, method_id))');
        $this->addSql('CREATE INDEX IDX_1E21D113F92F3E70 ON country_method (country_id)');
        $this->addSql('CREATE INDEX IDX_1E21D11319883967 ON country_method (method_id)');
        $this->addSql('CREATE TABLE currency (id SERIAL NOT NULL, iso VARCHAR(3) NOT NULL, rate DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq__currency_iso ON currency (iso)');
        $this->addSql('CREATE TABLE method (id SERIAL NOT NULL, name VARCHAR(40) NOT NULL, min_limit DOUBLE PRECISION DEFAULT NULL, max_limit DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE product (id SERIAL NOT NULL, currency_id INT NOT NULL, country_id INT DEFAULT NULL, title VARCHAR(100) NOT NULL, amount DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX product__currency_id__index ON product (currency_id)');
        $this->addSql('CREATE INDEX product__country_id__index ON product (country_id)');
        $this->addSql('CREATE TABLE purchase (id SERIAL NOT NULL, buyer_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX purchase__buyer_id__index ON purchase (buyer_id)');
        $this->addSql('COMMENT ON COLUMN purchase.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE purchase_product (purchase_id INT NOT NULL, product_id INT NOT NULL, PRIMARY KEY(purchase_id, product_id))');
        $this->addSql('CREATE INDEX IDX_C890CED4558FBEB9 ON purchase_product (purchase_id)');
        $this->addSql('CREATE INDEX IDX_C890CED44584665A ON purchase_product (product_id)');
        $this->addSql('CREATE TABLE transaction (id SERIAL NOT NULL, currency_id INT NOT NULL, payer_id INT NOT NULL, method_id INT NOT NULL, amount DOUBLE PRECISION NOT NULL, status SMALLINT NOT NULL, direction SMALLINT NOT NULL, payment_details VARCHAR(40) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX transaction__method_id__index ON transaction (method_id)');
        $this->addSql('CREATE INDEX transaction__payer_id__index ON transaction (payer_id)');
        $this->addSql('CREATE INDEX transaction__currency_id__index ON transaction (currency_id)');
        $this->addSql('COMMENT ON COLUMN transaction.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, currency_id INT NOT NULL, country_id INT NOT NULL, login VARCHAR(50) NOT NULL, balance DOUBLE PRECISION DEFAULT \'0\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq__user_login ON "user" (login)');
        $this->addSql('CREATE INDEX user__country_id__index ON "user" (country_id)');
        $this->addSql('CREATE INDEX user__currency_id__index ON "user" (currency_id)');
        $this->addSql('ALTER TABLE country_method ADD CONSTRAINT fk__country_method__country_id FOREIGN KEY (country_id) REFERENCES country (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE country_method ADD CONSTRAINT fk__country_method__method_id FOREIGN KEY (method_id) REFERENCES method (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT product__currency_id__index FOREIGN KEY (currency_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT product__country_id__index FOREIGN KEY (country_id) REFERENCES country (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT purchase__buyer_id__index FOREIGN KEY (buyer_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE purchase_product ADD CONSTRAINT fk__purchase_product__purchase_id FOREIGN KEY (purchase_id) REFERENCES purchase (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE purchase_product ADD CONSTRAINT fk__purchase_product__product_id FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT transaction__currency_id__index FOREIGN KEY (currency_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT transaction__payer_id__index FOREIGN KEY (payer_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT transaction__method_id__index FOREIGN KEY (method_id) REFERENCES method (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT user__currency_id__index FOREIGN KEY (currency_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT user__country_id__index FOREIGN KEY (country_id) REFERENCES country (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE country_method DROP CONSTRAINT fk__country_method__country_id');
        $this->addSql('ALTER TABLE country_method DROP CONSTRAINT fk__country_method__method_id');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT product__currency_id__index');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT product__country_id__index');
        $this->addSql('ALTER TABLE purchase DROP CONSTRAINT purchase__buyer_id__index');
        $this->addSql('ALTER TABLE purchase_product DROP CONSTRAINT fk__purchase_product__purchase_id');
        $this->addSql('ALTER TABLE purchase_product DROP CONSTRAINT fk__purchase_product__product_id');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT transaction__currency_id__index');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT transaction__payer_id__index');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT transaction__method_id__index');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT user__country_id__index');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT user__currency_id__index');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE country_method');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE method');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE purchase');
        $this->addSql('DROP TABLE purchase_product');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE "user"');
    }
}

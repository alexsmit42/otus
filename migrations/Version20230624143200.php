<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230624143200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase_product DROP CONSTRAINT fk__purchase_product__purchase_id');
        $this->addSql('ALTER TABLE purchase_product DROP CONSTRAINT fk__purchase_product__product_id');
        $this->addSql('ALTER TABLE purchase_product ADD CONSTRAINT fk__purchase_product__purchase_id FOREIGN KEY (purchase_id) REFERENCES purchase (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE purchase_product ADD CONSTRAINT fk__purchase_product__product_id FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase_product DROP CONSTRAINT fk__purchase_product__product_id');
        $this->addSql('ALTER TABLE purchase_product DROP CONSTRAINT fk__purchase_product__purchase_id');
        $this->addSql('ALTER TABLE purchase_product ADD CONSTRAINT fk__purchase_product__purchase_id FOREIGN KEY (purchase_id) REFERENCES purchase (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE purchase_product ADD CONSTRAINT fk__purchase_product__product_id FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220912112945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE feature_value_product DROP FOREIGN KEY FK_5156D6854584665A');
        $this->addSql('ALTER TABLE feature_value_product DROP FOREIGN KEY FK_5156D68580CD149D');
        $this->addSql('DROP TABLE feature_value_product');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE feature_value_product (feature_value_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_5156D68580CD149D (feature_value_id), INDEX IDX_5156D6854584665A (product_id), PRIMARY KEY(feature_value_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE feature_value_product ADD CONSTRAINT FK_5156D6854584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE feature_value_product ADD CONSTRAINT FK_5156D68580CD149D FOREIGN KEY (feature_value_id) REFERENCES feature_value (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}

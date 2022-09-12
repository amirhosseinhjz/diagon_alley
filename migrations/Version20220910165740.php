<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220910165740 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, city_id INT NOT NULL, description VARCHAR(255) DEFAULT NULL, post_code VARCHAR(20) NOT NULL, update_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_active TINYINT(1) NOT NULL, lat DOUBLE PRECISION NOT NULL, lng DOUBLE PRECISION NOT NULL, INDEX IDX_D4E6F81A76ED395 (user_id), INDEX IDX_D4E6F818BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE address_city (id INT AUTO_INCREMENT NOT NULL, province_id INT NOT NULL, name VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_5017D2DFE946114A (province_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE address_province (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_B4552A8A5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE brand (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(511) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, leaf TINYINT(1) NOT NULL, type VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_64C19C15E237E06 (name), INDEX IDX_64C19C1727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_feature (category_id INT NOT NULL, feature_id INT NOT NULL, INDEX IDX_D80F351812469DE2 (category_id), INDEX IDX_D80F351860E4B879 (feature_id), PRIMARY KEY(category_id, feature_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feature_value_product (feature_value_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_5156D68580CD149D (feature_value_id), INDEX IDX_5156D6854584665A (product_id), PRIMARY KEY(feature_value_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, brand_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(511) DEFAULT NULL, active TINYINT(1) NOT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', view_count INT NOT NULL, INDEX IDX_D34A04AD12469DE2 (category_id), INDEX IDX_D34A04AD44F5D008 (brand_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F81A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F818BAC62AF FOREIGN KEY (city_id) REFERENCES address_city (id)');
        $this->addSql('ALTER TABLE address_city ADD CONSTRAINT FK_5017D2DFE946114A FOREIGN KEY (province_id) REFERENCES address_province (id)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1727ACA70 FOREIGN KEY (parent_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE category_feature ADD CONSTRAINT FK_D80F351812469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_feature ADD CONSTRAINT FK_D80F351860E4B879 FOREIGN KEY (feature_id) REFERENCES feature (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE feature_value_product ADD CONSTRAINT FK_5156D68580CD149D FOREIGN KEY (feature_value_id) REFERENCES feature_value (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE feature_value_product ADD CONSTRAINT FK_5156D6854584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('ALTER TABLE cart CHANGE user_id customer_id INT NOT NULL');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B79395C3F3 FOREIGN KEY (customer_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_BA388B79395C3F3 ON cart (customer_id)');
        $this->addSql('ALTER TABLE cart_item ADD variant_id INT NOT NULL, ADD quantity INT NOT NULL, DROP varient_id, DROP count, DROP price, DROP title');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE25273B69A9AF FOREIGN KEY (variant_id) REFERENCES variant (id)');
        $this->addSql('CREATE INDEX IDX_F0FE25273B69A9AF ON cart_item (variant_id)');
        $this->addSql('ALTER TABLE purchase ADD address_id INT DEFAULT NULL, ADD total_price INT NOT NULL, ADD status INT NOT NULL');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13BF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('CREATE INDEX IDX_6117D13BF5B7AF75 ON purchase (address_id)');
        $this->addSql('ALTER TABLE purchase_item ADD quantity INT NOT NULL, ADD total_price INT NOT NULL');
        $this->addSql('ALTER TABLE variant ADD seller_id INT NOT NULL, ADD product_id INT NOT NULL, ADD type VARCHAR(30) NOT NULL');
        $this->addSql('ALTER TABLE variant ADD CONSTRAINT FK_F143BFAD8DE820D9 FOREIGN KEY (seller_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE variant ADD CONSTRAINT FK_F143BFAD4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_F143BFAD8DE820D9 ON variant (seller_id)');
        $this->addSql('CREATE INDEX IDX_F143BFAD4584665A ON variant (product_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13BF5B7AF75');
        $this->addSql('ALTER TABLE variant DROP FOREIGN KEY FK_F143BFAD4584665A');
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F81A76ED395');
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F818BAC62AF');
        $this->addSql('ALTER TABLE address_city DROP FOREIGN KEY FK_5017D2DFE946114A');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1727ACA70');
        $this->addSql('ALTER TABLE category_feature DROP FOREIGN KEY FK_D80F351812469DE2');
        $this->addSql('ALTER TABLE category_feature DROP FOREIGN KEY FK_D80F351860E4B879');
        $this->addSql('ALTER TABLE feature_value_product DROP FOREIGN KEY FK_5156D68580CD149D');
        $this->addSql('ALTER TABLE feature_value_product DROP FOREIGN KEY FK_5156D6854584665A');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD44F5D008');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE address_city');
        $this->addSql('DROP TABLE address_province');
        $this->addSql('DROP TABLE brand');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_feature');
        $this->addSql('DROP TABLE feature_value_product');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP INDEX IDX_6117D13BF5B7AF75 ON purchase');
        $this->addSql('ALTER TABLE purchase DROP address_id, DROP total_price, DROP status');
        $this->addSql('ALTER TABLE purchase_item DROP quantity, DROP total_price');
        $this->addSql('ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE25273B69A9AF');
        $this->addSql('DROP INDEX IDX_F0FE25273B69A9AF ON cart_item');
        $this->addSql('ALTER TABLE cart_item ADD varient_id INT NOT NULL, ADD count INT NOT NULL, ADD price INT NOT NULL, ADD title VARCHAR(255) NOT NULL, DROP variant_id, DROP quantity');
        $this->addSql('ALTER TABLE variant DROP FOREIGN KEY FK_F143BFAD8DE820D9');
        $this->addSql('DROP INDEX IDX_F143BFAD8DE820D9 ON variant');
        $this->addSql('DROP INDEX IDX_F143BFAD4584665A ON variant');
        $this->addSql('ALTER TABLE variant DROP seller_id, DROP product_id, DROP type');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B79395C3F3');
        $this->addSql('DROP INDEX IDX_BA388B79395C3F3 ON cart');
        $this->addSql('ALTER TABLE cart CHANGE customer_id user_id INT NOT NULL');
    }
}

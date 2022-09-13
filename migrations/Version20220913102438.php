<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220913102438 extends AbstractMigration
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
        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, finalized_at DATETIME DEFAULT NULL, status VARCHAR(8) NOT NULL, INDEX IDX_BA388B79395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cart_item (id INT AUTO_INCREMENT NOT NULL, cart_id INT NOT NULL, variant_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_F0FE25271AD5CDBF (cart_id), INDEX IDX_F0FE25273B69A9AF (variant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, leaf TINYINT(1) NOT NULL, type VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_64C19C15E237E06 (name), INDEX IDX_64C19C1727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_feature (category_id INT NOT NULL, feature_id INT NOT NULL, INDEX IDX_D80F351812469DE2 (category_id), INDEX IDX_D80F351860E4B879 (feature_id), PRIMARY KEY(category_id, feature_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feature (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feature_value (id INT AUTO_INCREMENT NOT NULL, feature_id INT NOT NULL, value VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, INDEX IDX_D429523D60E4B879 (feature_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feature_value_variant (feature_value_id INT NOT NULL, variant_id INT NOT NULL, INDEX IDX_735F6D8580CD149D (feature_value_id), INDEX IDX_735F6D853B69A9AF (variant_id), PRIMARY KEY(feature_value_id, variant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feature_value_product (feature_value_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_5156D68580CD149D (feature_value_id), INDEX IDX_5156D6854584665A (product_id), PRIMARY KEY(feature_value_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, portal_id INT DEFAULT NULL, purchase_id INT DEFAULT NULL, method VARCHAR(20) NOT NULL, paid_amount INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', status VARCHAR(20) NOT NULL, UNIQUE INDEX UNIQ_6D28840DB887E1DD (portal_id), INDEX IDX_6D28840D558FBEB9 (purchase_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE portal (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(20) NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, brand_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(511) DEFAULT NULL, active TINYINT(1) NOT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', view_count INT NOT NULL, INDEX IDX_D34A04AD12469DE2 (category_id), INDEX IDX_D34A04AD44F5D008 (brand_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE purchase (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, address_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', serial_number VARCHAR(25) NOT NULL, total_price INT NOT NULL, status VARCHAR(10) NOT NULL, INDEX IDX_6117D13B9395C3F3 (customer_id), INDEX IDX_6117D13BF5B7AF75 (address_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE purchase_item (id INT AUTO_INCREMENT NOT NULL, purchase_id INT NOT NULL, variant_id INT NOT NULL, paid_price INT NOT NULL, quantity INT NOT NULL, total_price INT NOT NULL, status VARCHAR(10) NOT NULL, INDEX IDX_6FA8ED7D558FBEB9 (purchase_id), INDEX IDX_6FA8ED7D3B69A9AF (variant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_tokens (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shipment (id INT AUTO_INCREMENT NOT NULL, seller_id INT NOT NULL, status VARCHAR(255) DEFAULT NULL, INDEX IDX_2CB20DC8DE820D9 (seller_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shipment_item (id INT AUTO_INCREMENT NOT NULL, shipment_id INT NOT NULL, purchase_item_id INT NOT NULL, status VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, INDEX IDX_1C573407BE036FC (shipment_id), UNIQUE INDEX UNIQ_1C573409B59827 (purchase_item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, phone_number VARCHAR(13) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', password VARCHAR(255) NOT NULL, token_validate_after DATETIME DEFAULT NULL, is_active TINYINT(1) NOT NULL, is_authenticated TINYINT(1) NOT NULL, type VARCHAR(255) NOT NULL, shop_slug VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D6496B01BC5B (phone_number), UNIQUE INDEX UNIQ_8D93D649596F7D19 (shop_slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE variant (id INT AUTO_INCREMENT NOT NULL, seller_id INT NOT NULL, product_id INT NOT NULL, serial VARCHAR(255) DEFAULT NULL, price BIGINT NOT NULL, quantity INT NOT NULL, valid TINYINT(1) NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', sold_number INT NOT NULL, type VARCHAR(30) NOT NULL, INDEX IDX_F143BFAD8DE820D9 (seller_id), INDEX IDX_F143BFAD4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F81A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F818BAC62AF FOREIGN KEY (city_id) REFERENCES address_city (id)');
        $this->addSql('ALTER TABLE address_city ADD CONSTRAINT FK_5017D2DFE946114A FOREIGN KEY (province_id) REFERENCES address_province (id)');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B79395C3F3 FOREIGN KEY (customer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE25271AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE25273B69A9AF FOREIGN KEY (variant_id) REFERENCES variant (id)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1727ACA70 FOREIGN KEY (parent_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE category_feature ADD CONSTRAINT FK_D80F351812469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_feature ADD CONSTRAINT FK_D80F351860E4B879 FOREIGN KEY (feature_id) REFERENCES feature (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE feature_value ADD CONSTRAINT FK_D429523D60E4B879 FOREIGN KEY (feature_id) REFERENCES feature (id)');
        $this->addSql('ALTER TABLE feature_value_variant ADD CONSTRAINT FK_735F6D8580CD149D FOREIGN KEY (feature_value_id) REFERENCES feature_value (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE feature_value_variant ADD CONSTRAINT FK_735F6D853B69A9AF FOREIGN KEY (variant_id) REFERENCES variant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE feature_value_product ADD CONSTRAINT FK_5156D68580CD149D FOREIGN KEY (feature_value_id) REFERENCES feature_value (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE feature_value_product ADD CONSTRAINT FK_5156D6854584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DB887E1DD FOREIGN KEY (portal_id) REFERENCES portal (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D558FBEB9 FOREIGN KEY (purchase_id) REFERENCES purchase (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B9395C3F3 FOREIGN KEY (customer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13BF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE purchase_item ADD CONSTRAINT FK_6FA8ED7D558FBEB9 FOREIGN KEY (purchase_id) REFERENCES purchase (id)');
        $this->addSql('ALTER TABLE purchase_item ADD CONSTRAINT FK_6FA8ED7D3B69A9AF FOREIGN KEY (variant_id) REFERENCES variant (id)');
        $this->addSql('ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC8DE820D9 FOREIGN KEY (seller_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE shipment_item ADD CONSTRAINT FK_1C573407BE036FC FOREIGN KEY (shipment_id) REFERENCES shipment (id)');
        $this->addSql('ALTER TABLE shipment_item ADD CONSTRAINT FK_1C573409B59827 FOREIGN KEY (purchase_item_id) REFERENCES purchase_item (id)');
        $this->addSql('ALTER TABLE variant ADD CONSTRAINT FK_F143BFAD8DE820D9 FOREIGN KEY (seller_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE variant ADD CONSTRAINT FK_F143BFAD4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F81A76ED395');
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F818BAC62AF');
        $this->addSql('ALTER TABLE address_city DROP FOREIGN KEY FK_5017D2DFE946114A');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B79395C3F3');
        $this->addSql('ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE25271AD5CDBF');
        $this->addSql('ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE25273B69A9AF');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1727ACA70');
        $this->addSql('ALTER TABLE category_feature DROP FOREIGN KEY FK_D80F351812469DE2');
        $this->addSql('ALTER TABLE category_feature DROP FOREIGN KEY FK_D80F351860E4B879');
        $this->addSql('ALTER TABLE feature_value DROP FOREIGN KEY FK_D429523D60E4B879');
        $this->addSql('ALTER TABLE feature_value_variant DROP FOREIGN KEY FK_735F6D8580CD149D');
        $this->addSql('ALTER TABLE feature_value_variant DROP FOREIGN KEY FK_735F6D853B69A9AF');
        $this->addSql('ALTER TABLE feature_value_product DROP FOREIGN KEY FK_5156D68580CD149D');
        $this->addSql('ALTER TABLE feature_value_product DROP FOREIGN KEY FK_5156D6854584665A');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DB887E1DD');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D558FBEB9');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD44F5D008');
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B9395C3F3');
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13BF5B7AF75');
        $this->addSql('ALTER TABLE purchase_item DROP FOREIGN KEY FK_6FA8ED7D558FBEB9');
        $this->addSql('ALTER TABLE purchase_item DROP FOREIGN KEY FK_6FA8ED7D3B69A9AF');
        $this->addSql('ALTER TABLE shipment DROP FOREIGN KEY FK_2CB20DC8DE820D9');
        $this->addSql('ALTER TABLE shipment_item DROP FOREIGN KEY FK_1C573407BE036FC');
        $this->addSql('ALTER TABLE shipment_item DROP FOREIGN KEY FK_1C573409B59827');
        $this->addSql('ALTER TABLE variant DROP FOREIGN KEY FK_F143BFAD8DE820D9');
        $this->addSql('ALTER TABLE variant DROP FOREIGN KEY FK_F143BFAD4584665A');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE address_city');
        $this->addSql('DROP TABLE address_province');
        $this->addSql('DROP TABLE brand');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE cart_item');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_feature');
        $this->addSql('DROP TABLE feature');
        $this->addSql('DROP TABLE feature_value');
        $this->addSql('DROP TABLE feature_value_variant');
        $this->addSql('DROP TABLE feature_value_product');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE portal');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE purchase');
        $this->addSql('DROP TABLE purchase_item');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE shipment');
        $this->addSql('DROP TABLE shipment_item');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE variant');
    }
}

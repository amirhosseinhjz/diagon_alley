<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220822062201 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE brand (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1C52F9585E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_64C19C15E237E06 (name), INDEX IDX_64C19C1727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_brand (category_id INT NOT NULL, brand_id INT NOT NULL, INDEX IDX_F461B61112469DE2 (category_id), INDEX IDX_F461B61144F5D008 (brand_id), PRIMARY KEY(category_id, brand_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_item_feature (category_id INT NOT NULL, item_feature_id INT NOT NULL, INDEX IDX_CE5D70A012469DE2 (category_id), INDEX IDX_CE5D70A0DD559073 (item_feature_id), PRIMARY KEY(category_id, item_feature_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_feature (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_D34A04AD12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1727ACA70 FOREIGN KEY (parent_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE category_brand ADD CONSTRAINT FK_F461B61112469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_brand ADD CONSTRAINT FK_F461B61144F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_item_feature ADD CONSTRAINT FK_CE5D70A012469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_item_feature ADD CONSTRAINT FK_CE5D70A0DD559073 FOREIGN KEY (item_feature_id) REFERENCES item_feature (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1727ACA70');
        $this->addSql('ALTER TABLE category_brand DROP FOREIGN KEY FK_F461B61112469DE2');
        $this->addSql('ALTER TABLE category_brand DROP FOREIGN KEY FK_F461B61144F5D008');
        $this->addSql('ALTER TABLE category_item_feature DROP FOREIGN KEY FK_CE5D70A012469DE2');
        $this->addSql('ALTER TABLE category_item_feature DROP FOREIGN KEY FK_CE5D70A0DD559073');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('DROP TABLE brand');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_brand');
        $this->addSql('DROP TABLE category_item_feature');
        $this->addSql('DROP TABLE item_feature');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

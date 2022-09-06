<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220903075747 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, finalized_at DATETIME NOT NULL, status VARCHAR(8) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cart_item (id INT AUTO_INCREMENT NOT NULL, cart_id INT NOT NULL, varient_id INT NOT NULL, count INT NOT NULL, price INT NOT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_F0FE25271AD5CDBF (cart_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE define_feature (id INT AUTO_INCREMENT NOT NULL, item_feature_id INT NOT NULL, value VARCHAR(255) NOT NULL, status TINYINT(1) NOT NULL, INDEX IDX_6C87CAB7DD559073 (item_feature_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_feature (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, status TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_value (id INT AUTO_INCREMENT NOT NULL, varient_id INT NOT NULL, item_feature_id INT DEFAULT NULL, value VARCHAR(255) NOT NULL, INDEX IDX_37D043E4A0F8EBB9 (varient_id), INDEX IDX_37D043E4DD559073 (item_feature_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, cart_id INT NOT NULL, type VARCHAR(30) NOT NULL, paid_amount INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', status VARCHAR(10) NOT NULL, code VARCHAR(10) NOT NULL, INDEX IDX_6D28840D1AD5CDBF (cart_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE varient (id INT AUTO_INCREMENT NOT NULL, serial VARCHAR(255) DEFAULT NULL, price BIGINT NOT NULL, quantity INT NOT NULL, status TINYINT(1) NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE25271AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
        $this->addSql('ALTER TABLE define_feature ADD CONSTRAINT FK_6C87CAB7DD559073 FOREIGN KEY (item_feature_id) REFERENCES item_feature (id)');
        $this->addSql('ALTER TABLE item_value ADD CONSTRAINT FK_37D043E4A0F8EBB9 FOREIGN KEY (varient_id) REFERENCES varient (id)');
        $this->addSql('ALTER TABLE item_value ADD CONSTRAINT FK_37D043E4DD559073 FOREIGN KEY (item_feature_id) REFERENCES item_feature (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D1AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE25271AD5CDBF');
        $this->addSql('ALTER TABLE define_feature DROP FOREIGN KEY FK_6C87CAB7DD559073');
        $this->addSql('ALTER TABLE item_value DROP FOREIGN KEY FK_37D043E4A0F8EBB9');
        $this->addSql('ALTER TABLE item_value DROP FOREIGN KEY FK_37D043E4DD559073');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D1AD5CDBF');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE cart_item');
        $this->addSql('DROP TABLE define_feature');
        $this->addSql('DROP TABLE item_feature');
        $this->addSql('DROP TABLE item_value');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE varient');
    }
}

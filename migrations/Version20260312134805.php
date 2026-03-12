<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260312134805 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_items (id INT AUTO_INCREMENT NOT NULL, quantity INT NOT NULL, price NUMERIC(10, 2) NOT NULL, status VARCHAR(255) NOT NULL, seller_id INT NOT NULL, created_at DATETIME NOT NULL, product_id INT DEFAULT NULL, order__id INT NOT NULL, INDEX IDX_62809DB04584665A (product_id), INDEX IDX_62809DB0251A8A50 (order__id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, total_amount NUMERIC(10, 2) NOT NULL, status VARCHAR(255) NOT NULL, shipping_address VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, price NUMERIC(10, 2) NOT NULL, stock INT DEFAULT NULL, type VARCHAR(255) NOT NULL, seller_id INT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB04584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB0251A8A50 FOREIGN KEY (order__id) REFERENCES orders (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB04584665A');
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB0251A8A50');
        $this->addSql('DROP TABLE order_items');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

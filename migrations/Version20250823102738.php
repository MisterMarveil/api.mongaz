<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250823102738 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity_log (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', driver_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', date DATE NOT NULL, orders_count INT NOT NULL, amount_sum NUMERIC(10, 2) NOT NULL, INDEX IDX_FD06F647C3423909 (driver_id), UNIQUE INDEX driver_date_unique (driver_id, date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', author_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', order_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', text LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9474526CF675F31B (author_id), INDEX IDX_9474526C8D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE driver_profile (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', vehicle VARCHAR(100) DEFAULT NULL, available TINYINT(1) NOT NULL, current_lat NUMERIC(10, 7) DEFAULT NULL, current_lon NUMERIC(10, 7) DEFAULT NULL, last_seen_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_44A8CE6FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_item (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', order_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', brand VARCHAR(50) NOT NULL, capacity VARCHAR(20) NOT NULL, quantity INT NOT NULL, INDEX IDX_52EA1F098D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `orders` (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', assigned_driver_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', created_by_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', customer_phone VARCHAR(20) NOT NULL, customer_name VARCHAR(120) DEFAULT NULL, address VARCHAR(255) NOT NULL, amount NUMERIC(10, 2) NOT NULL, status VARCHAR(40) NOT NULL, idempotency_key VARCHAR(64) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_E52FFDEE7FD1C147 (idempotency_key), INDEX IDX_E52FFDEEBAE38CAB (assigned_driver_id), INDEX IDX_E52FFDEEB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE point_of_sale (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(120) NOT NULL, address VARCHAR(255) NOT NULL, lat NUMERIC(10, 7) NOT NULL, lon NUMERIC(10, 7) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_tokens (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', phone VARCHAR(20) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, name VARCHAR(120) DEFAULT NULL, is_active TINYINT(1) NOT NULL, is_enabled TINYINT(1) NOT NULL, enabled_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', activation_code VARCHAR(6) DEFAULT NULL, activation_code_expires_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', reset_password_code VARCHAR(6) DEFAULT NULL, reset_password_code_expires_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649444F97DD (phone), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activity_log ADD CONSTRAINT FK_FD06F647C3423909 FOREIGN KEY (driver_id) REFERENCES driver_profile (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C8D9F6D38 FOREIGN KEY (order_id) REFERENCES `orders` (id)');
        $this->addSql('ALTER TABLE driver_profile ADD CONSTRAINT FK_44A8CE6FA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES `orders` (id)');
        $this->addSql('ALTER TABLE `orders` ADD CONSTRAINT FK_E52FFDEEBAE38CAB FOREIGN KEY (assigned_driver_id) REFERENCES driver_profile (id)');
        $this->addSql('ALTER TABLE `orders` ADD CONSTRAINT FK_E52FFDEEB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity_log DROP FOREIGN KEY FK_FD06F647C3423909');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C8D9F6D38');
        $this->addSql('ALTER TABLE driver_profile DROP FOREIGN KEY FK_44A8CE6FA76ED395');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F098D9F6D38');
        $this->addSql('ALTER TABLE `orders` DROP FOREIGN KEY FK_E52FFDEEBAE38CAB');
        $this->addSql('ALTER TABLE `orders` DROP FOREIGN KEY FK_E52FFDEEB03A8386');
        $this->addSql('DROP TABLE activity_log');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE driver_profile');
        $this->addSql('DROP TABLE order_item');
        $this->addSql('DROP TABLE `orders`');
        $this->addSql('DROP TABLE point_of_sale');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE `user`');
    }
}

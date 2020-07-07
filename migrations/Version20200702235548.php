<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200702235548 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE account (id INT AUTO_INCREMENT NOT NULL, account_number VARCHAR(255) NOT NULL, available_cash VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, governance_id INT DEFAULT NULL, account_id INT DEFAULT NULL, category_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, zip_code VARCHAR(255) NOT NULL, phone_number VARCHAR(255) NOT NULL, siret VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, validated TINYINT(1) NOT NULL, INDEX IDX_4FBF094F480A5856 (governance_id), UNIQUE INDEX UNIQ_4FBF094F9B6B5FBA (account_id), INDEX IDX_4FBF094F12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company_user (company_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_CEFECCA7979B1AD6 (company_id), INDEX IDX_CEFECCA7A76ED395 (user_id), PRIMARY KEY(company_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE currency (id INT AUTO_INCREMENT NOT NULL, governance_id INT NOT NULL, name VARCHAR(50) NOT NULL, short_name VARCHAR(4) NOT NULL, exchange_rate INT NOT NULL, UNIQUE INDEX UNIQ_6956883F480A5856 (governance_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE governance (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, money_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE governance_user_information (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, governance_id INT DEFAULT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_9BE634A1A76ED395 (user_id), INDEX IDX_9BE634A1480A5856 (governance_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE particular (id INT AUTO_INCREMENT NOT NULL, governance_id INT DEFAULT NULL, account_id INT DEFAULT NULL, user_id INT DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(255) DEFAULT NULL, validated TINYINT(1) NOT NULL, birthdate DATE NOT NULL, INDEX IDX_862161CF480A5856 (governance_id), UNIQUE INDEX UNIQ_862161CF9B6B5FBA (account_id), UNIQUE INDEX UNIQ_862161CFA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, company_id INT DEFAULT NULL, title VARCHAR(500) NOT NULL, content LONGTEXT NOT NULL, date DATE NOT NULL, INDEX IDX_5A8A6C8D979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, beneficiary_id INT DEFAULT NULL, emiter_id INT DEFAULT NULL, transfered_money VARCHAR(255) NOT NULL, date DATE NOT NULL, INDEX IDX_723705D1ECCAAFA0 (beneficiary_id), INDEX IDX_723705D13C6AAFC6 (emiter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F480A5856 FOREIGN KEY (governance_id) REFERENCES governance (id)');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F9B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE company_user ADD CONSTRAINT FK_CEFECCA7979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE company_user ADD CONSTRAINT FK_CEFECCA7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE currency ADD CONSTRAINT FK_6956883F480A5856 FOREIGN KEY (governance_id) REFERENCES governance (id)');
        $this->addSql('ALTER TABLE governance_user_information ADD CONSTRAINT FK_9BE634A1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE governance_user_information ADD CONSTRAINT FK_9BE634A1480A5856 FOREIGN KEY (governance_id) REFERENCES governance (id)');
        $this->addSql('ALTER TABLE particular ADD CONSTRAINT FK_862161CF480A5856 FOREIGN KEY (governance_id) REFERENCES governance (id)');
        $this->addSql('ALTER TABLE particular ADD CONSTRAINT FK_862161CF9B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE particular ADD CONSTRAINT FK_862161CFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1ECCAAFA0 FOREIGN KEY (beneficiary_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D13C6AAFC6 FOREIGN KEY (emiter_id) REFERENCES account (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F9B6B5FBA');
        $this->addSql('ALTER TABLE particular DROP FOREIGN KEY FK_862161CF9B6B5FBA');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1ECCAAFA0');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D13C6AAFC6');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F12469DE2');
        $this->addSql('ALTER TABLE company_user DROP FOREIGN KEY FK_CEFECCA7979B1AD6');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D979B1AD6');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F480A5856');
        $this->addSql('ALTER TABLE currency DROP FOREIGN KEY FK_6956883F480A5856');
        $this->addSql('ALTER TABLE governance_user_information DROP FOREIGN KEY FK_9BE634A1480A5856');
        $this->addSql('ALTER TABLE particular DROP FOREIGN KEY FK_862161CF480A5856');
        $this->addSql('ALTER TABLE company_user DROP FOREIGN KEY FK_CEFECCA7A76ED395');
        $this->addSql('ALTER TABLE governance_user_information DROP FOREIGN KEY FK_9BE634A1A76ED395');
        $this->addSql('ALTER TABLE particular DROP FOREIGN KEY FK_862161CFA76ED395');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE company_user');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE governance');
        $this->addSql('DROP TABLE governance_user_information');
        $this->addSql('DROP TABLE particular');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE user');
    }
}

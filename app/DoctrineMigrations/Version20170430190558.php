<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170430190558 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE inValue (id INT AUTO_INCREMENT NOT NULL, machine_id INT DEFAULT NULL, date DATE NOT NULL, value INT NOT NULL, created_at DATE NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_AB28384FF6B75B26 (machine_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE inValue ADD CONSTRAINT FK_AB28384FF6B75B26 FOREIGN KEY (machine_id) REFERENCES machines (id)');
        $this->addSql('DROP TABLE `in`');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE `in` (id INT AUTO_INCREMENT NOT NULL, machine_id INT DEFAULT NULL, date DATE NOT NULL, value INT NOT NULL, created_at DATE NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_5FEC8E4EF6B75B26 (machine_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `in` ADD CONSTRAINT FK_5FEC8E4EF6B75B26 FOREIGN KEY (machine_id) REFERENCES machines (id)');
        $this->addSql('DROP TABLE inValue');
    }
}

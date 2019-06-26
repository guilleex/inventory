<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170426161218 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE jackpots (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, date DATE NOT NULL, value INT NOT NULL, created_at DATE NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_C3A1AF16C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE jackpots ADD CONSTRAINT FK_C3A1AF16C54C8C93 FOREIGN KEY (type_id) REFERENCES machine_types (id)');
        $this->addSql('DROP TABLE jackpots_type');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE jackpots_type (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, date DATE NOT NULL, value INT NOT NULL, created_at DATE NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_133EFF99C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE jackpots_type ADD CONSTRAINT FK_133EFF99C54C8C93 FOREIGN KEY (type_id) REFERENCES machine_types (id)');
        $this->addSql('DROP TABLE jackpots');
    }
}

<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180108190915 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE income (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, created_at DATE NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_3FA862D0989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE income_input (id INT AUTO_INCREMENT NOT NULL, worker_id INT DEFAULT NULL, date DATE NOT NULL, value INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_434750E6B20BA36 (worker_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE worker (id INT AUTO_INCREMENT NOT NULL, income_id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATE NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_9FB2BF62640ED2C0 (income_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE income_input ADD CONSTRAINT FK_434750E6B20BA36 FOREIGN KEY (worker_id) REFERENCES worker (id)');
        $this->addSql('ALTER TABLE worker ADD CONSTRAINT FK_9FB2BF62640ED2C0 FOREIGN KEY (income_id) REFERENCES income (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE worker DROP FOREIGN KEY FK_9FB2BF62640ED2C0');
        $this->addSql('ALTER TABLE income_input DROP FOREIGN KEY FK_434750E6B20BA36');
        $this->addSql('DROP TABLE income');
        $this->addSql('DROP TABLE income_input');
        $this->addSql('DROP TABLE worker');
    }
}

<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170516215019 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE jackpots DROP FOREIGN KEY FK_C3A1AF16C54C8C93');
        $this->addSql('DROP INDEX IDX_C3A1AF16C54C8C93 ON jackpots');
        $this->addSql('ALTER TABLE jackpots CHANGE type_id machine_type_id INT NOT NULL');
        $this->addSql('ALTER TABLE jackpots ADD CONSTRAINT FK_C3A1AF16C3331C23 FOREIGN KEY (machine_type_id) REFERENCES machine_types (id)');
        $this->addSql('CREATE INDEX IDX_C3A1AF16C3331C23 ON jackpots (machine_type_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE jackpots DROP FOREIGN KEY FK_C3A1AF16C3331C23');
        $this->addSql('DROP INDEX IDX_C3A1AF16C3331C23 ON jackpots');
        $this->addSql('ALTER TABLE jackpots CHANGE machine_type_id type_id INT NOT NULL');
        $this->addSql('ALTER TABLE jackpots ADD CONSTRAINT FK_C3A1AF16C54C8C93 FOREIGN KEY (type_id) REFERENCES machine_types (id)');
        $this->addSql('CREATE INDEX IDX_C3A1AF16C54C8C93 ON jackpots (type_id)');
    }
}

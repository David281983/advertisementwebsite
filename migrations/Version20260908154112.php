<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260908154112 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE advertisement_image (id INT AUTO_INCREMENT NOT NULL, filename VARCHAR(255) NOT NULL, position INT NOT NULL, advertisement_id INT NOT NULL, INDEX IDX_BDD94FE6A1FBF71B (advertisement_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE advertisement_image ADD CONSTRAINT FK_BDD94FE6A1FBF71B FOREIGN KEY (advertisement_id) REFERENCES advertisement (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE advertisement_image DROP FOREIGN KEY FK_BDD94FE6A1FBF71B');
        $this->addSql('DROP TABLE advertisement_image');
    }
}

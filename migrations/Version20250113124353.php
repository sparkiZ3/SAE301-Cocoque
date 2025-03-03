<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250113124353 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE collections (id INT AUTO_INCREMENT NOT NULL, collection_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_S_produit');
        $this->addSql('DROP INDEX U_S_produit ON stock');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE collections');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_S_produit FOREIGN KEY (id_produit) REFERENCES product (id)');
        $this->addSql('CREATE UNIQUE INDEX U_S_produit ON stock (id_produit)');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260120182023 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE operacion (id INT AUTO_INCREMENT NOT NULL, tipo_operacion VARCHAR(50) NOT NULL, fecha DATETIME NOT NULL, usuario_id INT NOT NULL, articulo_id INT NOT NULL, INDEX IDX_D44FC94BDB38439E (usuario_id), INDEX IDX_D44FC94B2DBC2FC9 (articulo_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE operacion ADD CONSTRAINT FK_D44FC94BDB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE operacion ADD CONSTRAINT FK_D44FC94B2DBC2FC9 FOREIGN KEY (articulo_id) REFERENCES articulo (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE operacion DROP FOREIGN KEY FK_D44FC94BDB38439E');
        $this->addSql('ALTER TABLE operacion DROP FOREIGN KEY FK_D44FC94B2DBC2FC9');
        $this->addSql('DROP TABLE operacion');
    }
}

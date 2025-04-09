<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration modifiée : Version20250407115757
 *
 * Remarque : La colonne "created_by_id" existe déjà dans la table "book".
 * Nous supprimons donc la commande d'ajout et de suppression de la colonne.
 */
final class Version20250407115757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute la contrainte de clé étrangère et l\'index sur la colonne created_by_id de la table book';
    }

    public function up(Schema $schema): void
    {
        // La colonne "created_by_id" existe déjà, donc on ne la recrée pas.
        // $this->addSql('ALTER TABLE book ADD created_by_id INT NOT NULL');

        // Ajout de la contrainte de clé étrangère reliant "created_by_id" de "book" à "id" de "user".
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        
        // Création de l'index sur la colonne "created_by_id".
        $this->addSql('CREATE INDEX IDX_CBE5A331B03A8386 ON book (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // Suppression de la contrainte de clé étrangère.
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331B03A8386');
        
        // Suppression de l'index sur la colonne "created_by_id".
        $this->addSql('DROP INDEX IDX_CBE5A331B03A8386 ON book');
        
        // On ne supprime pas la colonne "created_by_id" puisqu'elle existait déjà avant cette migration.
        // $this->addSql('ALTER TABLE book DROP created_by_id');
    }
}

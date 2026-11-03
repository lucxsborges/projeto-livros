<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261102235030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }
    
    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE VIEW vw_relatorio_livros AS
            SELECT
                a.CodAu AS autor_id,
                a.Nome AS autor_nome,
                l.Codl AS livro_id,
                l.Titulo AS livro_titulo,
                l.Editora AS livro_editora,
                l.Edicao AS livro_edicao,
                l.AnoPublicacao AS livro_ano,
                l.Valor AS livro_valor,
                s.codAs AS assunto_id,
                s.Descricao AS assunto_descricao
            FROM Autor a
            INNER JOIN Livro_Autor la
                ON la.Autor_CodAu = a.CodAu
            INNER JOIN Livro l
                ON l.Codl = la.Livro_Codl
            LEFT JOIN Livro_Assunto las
                ON las.Livro_Codl = l.Codl
            LEFT JOIN Assunto s
                ON s.codAs = las.Assunto_codAs
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP VIEW IF EXISTS vw_relatorio_livros');
    }
}

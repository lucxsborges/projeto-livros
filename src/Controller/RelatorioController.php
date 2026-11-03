<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RelatorioController extends AbstractController
{
    #[Route('/relatorio/livros', name: 'app_relatorio_livros', methods: ['GET'])]
    public function livros(Connection $connection): Response
    {
        return $this->render('relatorio/livros.html.twig', [
            'autores' => $this->buscarLivrosPorAutor($connection),
        ]);
    }

    #[Route('/relatorio/livros/pdf', name: 'app_relatorio_livros_pdf', methods: ['GET'])]
    public function livrosPdf(Connection $connection): Response
    {
        $autores = $this->buscarLivrosPorAutor($connection);

        $html = $this->renderView('relatorio/livros_pdf.html.twig', [
            'autores' => $autores,
            'dataGeracao' => new \DateTimeImmutable(),
        ]);

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="relatorio-livros-por-autor.pdf"',
            ]
        );
    }

    private function buscarLivrosPorAutor(Connection $connection): array
    {
        $registros = $connection->executeQuery(
            'SELECT *
             FROM vw_relatorio_livros
             ORDER BY autor_nome, livro_titulo, assunto_descricao'
        )->fetchAllAssociative();

        $autores = [];

        foreach ($registros as $registro) {
            $autorId = $registro['autor_id'];
            $livroId = $registro['livro_id'];

            if (!isset($autores[$autorId])) {
                $autores[$autorId] = [
                    'nome' => $registro['autor_nome'],
                    'livros' => [],
                ];
            }

            if (!isset($autores[$autorId]['livros'][$livroId])) {
                $autores[$autorId]['livros'][$livroId] = [
                    'titulo' => $registro['livro_titulo'],
                    'editora' => $registro['livro_editora'],
                    'edicao' => $registro['livro_edicao'],
                    'ano' => $registro['livro_ano'],
                    'valor' => $registro['livro_valor'],
                    'assuntos' => [],
                ];
            }

            if ($registro['assunto_id'] !== null) {
                $autores[$autorId]['livros'][$livroId]['assuntos'][
                    $registro['assunto_id']
                ] = $registro['assunto_descricao'];
            }
        }

        return $autores;
    }
}

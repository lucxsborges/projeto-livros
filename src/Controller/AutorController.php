<?php

namespace App\Controller;

use App\Entity\Autor;
use App\Form\AutorType;
use App\Repository\AutorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/autor')]
final class AutorController extends AbstractController
{
    #[Route(name: 'app_autor_index', methods: ['GET'])]
    public function index(AutorRepository $autorRepository): Response
    {
        return $this->render('autor/index.html.twig', [
            'autors' => $autorRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_autor_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $autor = new Autor();
        $form = $this->createForm(AutorType::class, $autor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($autor);
            $entityManager->flush();

            return $this->redirectToRoute('app_autor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('autor/new.html.twig', [
            'autor' => $autor,
            'form' => $form,
        ]);
    }

    #[Route('/{codAu}', name: 'app_autor_show', methods: ['GET'])]
    public function show(Autor $autor): Response
    {
        return $this->render('autor/show.html.twig', [
            'autor' => $autor,
        ]);
    }

    #[Route('/{codAu}/edit', name: 'app_autor_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Autor $autor, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AutorType::class, $autor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_autor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('autor/edit.html.twig', [
            'autor' => $autor,
            'form' => $form,
        ]);
    }
    
    #[Route('/{codAu}', name: 'app_autor_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Autor $autor,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->isCsrfTokenValid(
            'delete'.$autor->getCodAu(),
            $request->getPayload()->getString('_token')
        )) {
            $this->addFlash('danger', 'Não foi possível validar a solicitação de exclusão.');

            return $this->redirectToRoute('app_autor_index');
        }

        // Impede a exclusão de autores vinculados a livros.
        if (!$autor->getLivros()->isEmpty()) {
            $this->addFlash(
                'warning',
                'Este autor não pode ser excluído porque possui livros vinculados.'
            );

            return $this->redirectToRoute('app_autor_index');
        }

        // Exclui somente quando não existem vínculos.
        $entityManager->remove($autor);
        $entityManager->flush();

        $this->addFlash('success', 'Autor excluído com sucesso.');

        return $this->redirectToRoute('app_autor_index');
    }

}

<?php

namespace App\Controller;

use App\Entity\Assunto;
use App\Form\AssuntoType;
use App\Repository\AssuntoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/assunto')]
final class AssuntoController extends AbstractController
{
    #[Route(name: 'app_assunto_index', methods: ['GET'])]
    public function index(AssuntoRepository $assuntoRepository): Response
    {
        return $this->render('assunto/index.html.twig', [
            'assuntos' => $assuntoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_assunto_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $assunto = new Assunto();
        $form = $this->createForm(AssuntoType::class, $assunto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($assunto);
            $entityManager->flush();

            return $this->redirectToRoute('app_assunto_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('assunto/new.html.twig', [
            'assunto' => $assunto,
            'form' => $form,
        ]);
    }

    #[Route('/{codAs}', name: 'app_assunto_show', methods: ['GET'])]
    public function show(Assunto $assunto): Response
    {
        return $this->render('assunto/show.html.twig', [
            'assunto' => $assunto,
        ]);
    }

    #[Route('/{codAs}/edit', name: 'app_assunto_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Assunto $assunto, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AssuntoType::class, $assunto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_assunto_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('assunto/edit.html.twig', [
            'assunto' => $assunto,
            'form' => $form,
        ]);
    }
    
    #[Route('/{codAs}', name: 'app_assunto_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Assunto $assunto,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->isCsrfTokenValid(
            'delete'.$assunto->getCodAs(),
            $request->getPayload()->getString('_token')
        )) {
            $this->addFlash(
                'danger',
                'Não foi possível validar a solicitação de exclusão.'
            );

            return $this->redirectToRoute('app_assunto_index');
        }

        // Verifica se o assunto possui livros vinculados.
        if (!$assunto->getLivros()->isEmpty()) {
            $this->addFlash(
                'warning',
                'Este assunto não pode ser excluído porque possui livros vinculados.'
            );

            return $this->redirectToRoute('app_assunto_index');
        }

        // Exclui o assunto somente se não houver vínculos.
        $entityManager->remove($assunto);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Assunto excluído com sucesso.'
        );

        return $this->redirectToRoute('app_assunto_index');
    }
}

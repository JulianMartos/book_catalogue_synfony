<?php

namespace App\Controller;

use App\Entity\Editorial;
use App\Form\EditorialType;
use App\Repository\EditorialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/editorial')]
class EditorialController extends AbstractController
{
    #[Route('/', name: 'editorial_index', methods: ['GET'])]
    public function index(EditorialRepository $editorialRepository): Response
    {
        return $this->render('editorial/index.html.twig', [
            'editorials' => $editorialRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'editorial_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $editorial = new Editorial();
        $form = $this->createForm(EditorialType::class, $editorial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($editorial);
            $entityManager->flush();

            return $this->redirectToRoute('editorial_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('editorial/new.html.twig', [
            'editorial' => $editorial,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'editorial_show', methods: ['GET'])]
    public function show(Editorial $editorial): Response
    {
        return $this->render('editorial/show.html.twig', [
            'editorial' => $editorial,
        ]);
    }

    #[Route('/{id}/edit', name: 'editorial_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Editorial $editorial): Response
    {
        $form = $this->createForm(EditorialType::class, $editorial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('editorial_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('editorial/edit.html.twig', [
            'editorial' => $editorial,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'editorial_delete', methods: ['POST'])]
    public function delete(Request $request, Editorial $editorial): Response
    {
        if ($this->isCsrfTokenValid('delete'.$editorial->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($editorial);
            $entityManager->flush();
        }

        return $this->redirectToRoute('editorial_index', [], Response::HTTP_SEE_OTHER);
    }
}

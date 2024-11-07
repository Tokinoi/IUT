<?php

namespace App\Controller;

use App\Entity\Rencontre;
use App\Form\RencontreType;
use App\Repository\RencontreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/rencontre')]
class RencontreController extends AbstractController
{
    #[Route('/', name: 'app_rencontre_index', methods: ['GET'])]
    public function index(RencontreRepository $rencontreRepository): Response
    {
        return $this->render('rencontre/index.html.twig', [
            'rencontres' => $rencontreRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_rencontre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RencontreRepository $rencontreRepository): Response
    {
        $rencontre = new Rencontre();
        $form = $this->createForm(RencontreType::class, $rencontre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rencontreRepository->save($rencontre, true);

            return $this->redirectToRoute('app_rencontre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rencontre/new.html.twig', [
            'rencontre' => $rencontre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rencontre_show', methods: ['GET'])]
    public function show(Rencontre $rencontre): Response
    {
        return $this->render('rencontre/show.html.twig', [
            'rencontre' => $rencontre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_rencontre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rencontre $rencontre, RencontreRepository $rencontreRepository): Response
    {
        $form = $this->createForm(RencontreType::class, $rencontre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rencontreRepository->save($rencontre, true);

            return $this->redirectToRoute('app_rencontre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rencontre/edit.html.twig', [
            'rencontre' => $rencontre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rencontre_delete', methods: ['POST'])]
    public function delete(Request $request, Rencontre $rencontre, RencontreRepository $rencontreRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rencontre->getId(), $request->request->get('_token'))) {
            $rencontreRepository->remove($rencontre, true);
        }

        return $this->redirectToRoute('app_rencontre_index', [], Response::HTTP_SEE_OTHER);
    }
}

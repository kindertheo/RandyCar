<?php

namespace App\Controller;

use App\Entity\Opinion;
use App\Form\OpinionType;
use App\Repository\OpinionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/opinion")
 */
class OpinionController extends AbstractController
{
    /**
     * @Route("/", name="app_opinion_index", methods={"GET"})
     */
    public function index(OpinionRepository $opinionRepository): Response
    {
        return $this->render('opinion/index.html.twig', [
            'opinions' => $opinionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_opinion_new", methods={"GET", "POST"})
     */
    public function new(Request $request, OpinionRepository $opinionRepository): Response
    {
        $opinion = new Opinion();
        $form = $this->createForm(OpinionType::class, $opinion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $opinionRepository->add($opinion, true);

            return $this->redirectToRoute('app_opinion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('opinion/new.html.twig', [
            'opinion' => $opinion,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_opinion_show", methods={"GET"})
     */
    public function show(Opinion $opinion): Response
    {
        return $this->render('opinion/show.html.twig', [
            'opinion' => $opinion,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_opinion_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Opinion $opinion, OpinionRepository $opinionRepository): Response
    {
        $form = $this->createForm(OpinionType::class, $opinion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $opinionRepository->add($opinion, true);

            return $this->redirectToRoute('app_opinion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('opinion/edit.html.twig', [
            'opinion' => $opinion,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_opinion_delete", methods={"POST"})
     */
    public function delete(Request $request, Opinion $opinion, OpinionRepository $opinionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$opinion->getId(), $request->request->get('_token'))) {
            $opinionRepository->remove($opinion, true);
        }

        return $this->redirectToRoute('app_opinion_index', [], Response::HTTP_SEE_OTHER);
    }
}

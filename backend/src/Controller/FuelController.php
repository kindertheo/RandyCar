<?php

namespace App\Controller;

use App\Entity\Fuel;
use App\Form\FuelType;
use App\Repository\FuelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/fuel")
 */
class FuelController extends AbstractController
{
    /**
     * @Route("/", name="app_fuel_index", methods={"GET"})
     */
    public function index(FuelRepository $fuelRepository): Response
    {
        return $this->render('fuel/index.html.twig', [
            'fuels' => $fuelRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_fuel_new", methods={"GET", "POST"})
     */
    public function new(Request $request, FuelRepository $fuelRepository): Response
    {
        $fuel = new Fuel();
        $form = $this->createForm(FuelType::class, $fuel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fuelRepository->add($fuel, true);

            return $this->redirectToRoute('app_fuel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('fuel/new.html.twig', [
            'fuel' => $fuel,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_fuel_show", methods={"GET"})
     */
    public function show(Fuel $fuel): Response
    {
        return $this->render('fuel/show.html.twig', [
            'fuel' => $fuel,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_fuel_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Fuel $fuel, FuelRepository $fuelRepository): Response
    {
        $form = $this->createForm(FuelType::class, $fuel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fuelRepository->add($fuel, true);

            return $this->redirectToRoute('app_fuel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('fuel/edit.html.twig', [
            'fuel' => $fuel,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_fuel_delete", methods={"POST"})
     */
    public function delete(Request $request, Fuel $fuel, FuelRepository $fuelRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fuel->getId(), $request->request->get('_token'))) {
            $fuelRepository->remove($fuel, true);
        }

        return $this->redirectToRoute('app_fuel_index', [], Response::HTTP_SEE_OTHER);
    }
}

<?php

namespace App\Controller;

use App\Factory\ResultsFactory;
use App\Form\ResultType;
use App\Repository\ResultsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ResultsController extends AbstractController
{

    private ResultsFactory $resultsFactory;

    /**
     * @param ResultsFactory $resultsFactory
     */
    public function __construct(ResultsFactory $resultsFactory)
    {
        $this->resultsFactory = $resultsFactory;
    }

    /**
     * @Route("/results/all", name="app_results_all")
     */
    public function index(ResultsRepository $resultsRepository): Response
    {
        $results = $resultsRepository->findAll();

        return $this->render('results/index.html.twig', [
            'results' => $results,
        ]);
    }

    /**
     * @Route("/results/edit/{id}", name="app_results_edit")
     * @param Request $request
     * @param int $id
     * @param ResultsRepository $resultsRepository
     * @return Response
     */
    public function edit(Request $request, int $id, ResultsRepository $resultsRepository): Response
    {
        $result = $resultsRepository->find($id);

        $form = $this->createForm(ResultType::class, $result);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $resultsRepository->add($result, true);

            $this->resultsFactory->persistPlacement($result->getRace(), $result->getDistance());

            return $this->redirectToRoute('app_race_show', ['id' => $result->getRace()->getId()], RESPONSE::HTTP_SEE_OTHER);
        }

        return $this->render('results/edit.html.twig', ['result' => $result, 'form' => $form->createView()]);
    }
}
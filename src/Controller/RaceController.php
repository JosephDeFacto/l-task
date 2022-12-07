<?php

namespace App\Controller;

use App\Calculation\Average;
use App\Factory\RaceFactory;
use App\Factory\ResultsFactory;
use App\Form\RaceType;
use App\Helper\Distance;
use App\Repository\RaceRepository;
use App\Repository\ResultsRepository;
use App\Services\Uploader\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RaceController extends AbstractController
{

    private FileUploader $fileUploader;

    private EntityManagerInterface $entityManager;

    private ResultsFactory $resultsFactory;

    private RaceFactory $raceFactory;

    private Distance $distance;

    private ResultsRepository $resultsRepository;

    private RaceRepository $raceRepository;

    private Average $average;


    /**
     * __construct
     *
     * @param FileUploader $fileUploader
     * @param EntityManagerInterface $entityManager
     * @param ResultsFactory $resultsFactory
     * @param RaceFactory $raceFactory
     * @param ResultsRepository $resultsRepository
     * @param Average $average
     * @param RaceRepository $raceRepository
     */
    public function __construct(FileUploader $fileUploader,
                                EntityManagerInterface $entityManager,
                                ResultsFactory $resultsFactory,
                                RaceFactory $raceFactory,
                                ResultsRepository $resultsRepository,
                                Average $average,
                                RaceRepository $raceRepository,
                                Distance $distance
                                )
    {

        $this->fileUploader = $fileUploader;
        $this->entityManager = $entityManager;
        $this->resultsFactory = $resultsFactory;
        $this->raceFactory = $raceFactory;
        $this->resultsRepository = $resultsRepository;
        $this->average = $average;
        $this->raceRepository = $raceRepository;
        $this->distance = $distance;
    }

    /**
     * @Route("/race/new", name="app_race_new")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function new(Request $request): Response
    {
        $race = $this->raceFactory->build('Race');

        $form = $this->createForm(RaceType::class, $race);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();


            if ($file) {
                $this->fileUploader->upload($file);
            }
            $this->raceFactory->persist($race, $this->entityManager);

            $this->resultsFactory->build('Results', $this->fileUploader, $this->entityManager, $race);

            foreach ($this->distance->getDistance() as $distance) {
                $this->resultsFactory->persistPlacement($race, $distance);
            }


            $this->fileUploader->delete();

            $this->addFlash('success', 'Success');

            return $this->redirectToRoute('app_race_all');

        }

        return $this->render('race/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/race/all", name="app_race_all")
     */
    public function all(): Response
    {
        return $this->render('race/index.html.twig', [
            'races' => $this->raceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/race/{id}", name="app_race_show")
     * @throws Exception
     */
    public function show($id): Response
    {
        $results = $this->resultsRepository->findResultsByRaceId($id);

        if (!$results) {
            throw $this->createNotFoundException('No results for id: ' . $id);
        }

        $averages = $this->average->average($id);

        $medium = $averages["medium"]["AvgTime"];
        $long = $averages["long"]["AvgTime"];


        return $this->render('results/index.html.twig', [
            'medium' => $medium,
            'long' => $long,
            'race' => $this->raceRepository->findOneBy(['id' => $id]),
            'results' => $results,
        ]);
    }
}
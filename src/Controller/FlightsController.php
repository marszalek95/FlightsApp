<?php

namespace App\Controller;

use App\Form\SaveFormType;
use App\Form\SearchFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\FlightsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;



class FlightsController extends AbstractController
{
    private $flightService;

    public function __construct(FlightsService $flightService) {
        $this->flightService = $flightService;
    }

    #[Route('/flights', name: 'app_flights')]
    public function index(): Response
    {
        return $this->render('flights/index.html.twig', [
            
        ]);
    }

    #[Route('/addflight/search/{departure}/{destination}/{type}', name: 'app_addflight_result',)]
    public function addFlight(Request $request, $departure = null, $destination = null, $type = null): Response
    {   
        $form = $this->createForm(SearchFormType::class, null, [
            'departure' => $departure,
            'destination' => $destination,
            'actualtype' => $type,
            'action' => $this->generateUrl('app_addflight'),
        ]);
        $saveform = $this->createForm(SaveFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $type = $form->getData()['type'];
            $departure = $form->getData()['departure'];
            $destination = $form->getData()['destination'];   
            return $this->redirect("/addflight/search/{$departure}/{$destination}/{$type}");
        }

        if($type === 'return') {
            $data = [
                'form' => $form,
                'saveform' => $saveform,
                'data_departure' => $this->flightService->searchFlight($departure, $destination),
                'data_return' => $this->flightService->searchFlight($destination, $departure),
                'type' => $type,
                'departure' => $departure,
                'destination' => $destination
            ];
        }
        elseif($type === 'oneWay') {
            $data = [
                'form' => $form,
                'saveform' => $saveform,
                'data_departure' => $this->flightService->searchFlight($departure, $destination),
                'type' => $type,
                'departure' => $departure,
                'destination' => $destination
            ];
        }

            // Return a response (you may want to render a template or return JSON, depending on your needs)
            return $this->render('flights/add_flight.html.twig', $data);
         
    }

    #[Route('/save-flight', name: 'app_save_flight', methods: ['POST'])]
    public function saveFlight(EntityManagerInterface $entityManager, Request $request): Response
    {
        $outboundKey = $request->request->get('outbound_key');
        $returnKey = $request->request->get('return_key');
        $flightsJson = $request->request->get('flights');
        $flights = json_decode($flightsJson);

        foreach ($flights->trips[0]->dates[0]->flights as $key => $flight) {
            if($key != $outboundKey) {
                unset($flights->trips[0]->dates[0]->flights[$key]);
            }
        }

        foreach ($flights->trips[1]->dates[0]->flights as $key => $flight) {
            if($key != $returnKey) {
                unset($flights->trips[1]->dates[0]->flights[$key]);
            }
        }

        sort($flights->trips[0]->dates[0]->flights);
        sort($flights->trips[1]->dates[0]->flights);

        $this->flightService->saveFlights($entityManager, $flights);

        return $this->redirectToRoute('app_flights');

    }

}

<?php

namespace App\Controller;

use App\Entity\Flight;
use App\Entity\FlightPrices;
use App\Form\SaveFormType;
use App\Form\SearchFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\FlightsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FlightsController extends AbstractController
{
    private $flightService;

    public function __construct(FlightsService $flightService) {
        $this->flightService = $flightService;
    }

    #[Route('/flights', name: 'app_flights')]
    public function flights(EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $flights = $entityManager->getRepository(Flight::class)->findby(['user_id' => $user->getId()]);

        $processedReturnFlights = [];

        $flightsData = [];
        foreach ($flights as $flight) {
            // Check if this flight has a return flight
            if (in_array($flight->getId(), $processedReturnFlights)) {
                continue;
            }
    
            if ($flight->getReturnFlight()) {
                $returnFlight = $entityManager->getRepository(Flight::class)->find($flight->getReturnFlight());
    
                // Ensure the return flight exists and hasn't already been processed
                if ($returnFlight && !in_array($returnFlight->getId(), $processedReturnFlights)) {
                    $flightsData[] = [
                        'flight' => $flight,
                        'returnFlight' => $returnFlight,
                        'prices' => $entityManager->getRepository(FlightPrices::class)->findBy(['flight_id' => $flight->getId()],['recorded_at' => 'ASC']),
                        'returnPrices' => $entityManager->getRepository(FlightPrices::class)->findBy(['flight_id' => $returnFlight->getId()],['recorded_at' => 'ASC']),
                    ];
    
                    // Mark the return flight as processed
                    $processedReturnFlights[] = $returnFlight->getId();
                }
            } else {
                $flightsData[] = [
                    'flight' => $flight,
                    'returnFlight' => null,
                    'prices' => $entityManager->getRepository(FlightPrices::class)->findBy(['flight_id' => $flight->getId()])
                ];
            }
        }

        

        return $this->render('flights/flights.html.twig', [
            'flightsData' => $flightsData,
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

    #[Route('/chooseflight', name: 'app_save_flight')]
    public function saveFlight(EntityManagerInterface $entityManager, Request $request, SessionInterface $session): Response
    {
        $obj = $session->get('flights');

        if($request->isMethod('POST')) {
        $outboundKey = $request->request->get('outbound_key');
        $returnKey = $request->request->get('return_key');
        $flightsJson = $request->request->get('flights');
        $flights = json_decode($flightsJson);

        foreach ($flights->trips[0]->dates[0]->flights as $key => $flight) {
            if($key != $outboundKey) {
                unset($flights->trips[0]->dates[0]->flights[$key]);
            }
        }
        sort($flights->trips[0]->dates[0]->flights);

        if (isset($returnKey)) {
            foreach ($flights->trips[1]->dates[0]->flights as $key => $flight) {
                if($key != $returnKey) {
                    unset($flights->trips[1]->dates[0]->flights[$key]);
                }
            }
            sort($flights->trips[1]->dates[0]->flights);
        }

        $this->flightService->saveFlights($entityManager, $flights);
        return $this->redirectToRoute('app_flights');
        }

        return $this->render('flights/choose_flight.html.twig', ['flights' => $obj]);

    }

}

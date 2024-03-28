<?php

namespace App\Controller;

use App\Form\SearchFormType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\FlightsService;

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
    public function addFlight($departure = null, $destination = null, $type = null): Response
    {   
        $form = $this->createForm(SearchFormType::class, null, [
            'departure' => $departure,
            'destination' => $destination,
            'actualtype' => $type,
            'action' => $this->generateUrl('app_addflight'),
        ]);

        if($type === 'return') {
            $data = [
                'form' => $form->createView(),
                'data_departure' => $this->flightService->searchFlight($departure, $destination),
                'data_return' => $this->flightService->searchFlight($destination, $departure),
                'type' => $type,
            ];
        }
        elseif($type === 'oneWay') {
            $data = [
                'form' => $form->createView(),
                'data_departure' => $this->flightService->searchFlight($departure, $destination),
                'type' => $type,
            ];
        }

            // Return a response (you may want to render a template or return JSON, depending on your needs)
            return $this->render('flights/add_flight.html.twig', $data);
         
    }

}

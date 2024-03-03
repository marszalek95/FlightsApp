<?php

namespace App\Controller;

use App\Form\SearchFormType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpClient\HttpClient;

class FlightsController extends AbstractController
{
    #[Route('/flights', name: 'app_flights')]
    public function index(): Response
    {
        return $this->render('flights/index.html.twig', [
            
        ]);
    }

    #[Route('/addflight/search/{departure}/{destination}', name: 'app_addflight_result',)]
    // #[Route('/addflight', name: 'app_addflight')]
    public function addFlight($departure = null, $destination = null): Response
    {
        $currentDate = new DateTime('now');
        $year = $currentDate->format('Y');
        $month = $currentDate->format('m');
        // Replace this URL with your actual API endpoint
        $apiUrl = "https://services-api.ryanair.com/timtbl/3/schedules/{$departure}/{$destination}/years/{$year}/months/{$month}";

        $form = $this->createForm(SearchFormType::class, null, [
            'departure' => $departure,
            'destination' => $destination,
            'action' => $this->generateUrl('app_addflight'),
        ]);


        // Create an instance of HttpClient
        $httpClient = HttpClient::create();

        // Make a GET request to the API
        $response = $httpClient->request('GET', $apiUrl);

        // Check if the request was successful (status code 200)
        if ($response->getStatusCode() === 200) {
            // Decode the JSON response
            $data = $response->toArray();
            $data = json_encode($data);
            $obj = json_decode($data);
            foreach($obj->days as $days) {
               $date = new DateTime("$year-$month-$days->day"); 
               $data_callendar[] = array(
                'title' => $days->flights[0]->number,
                'start' => $date->format('Y-m-d'),
               ); 
            }

            // Process the data as needed
            // ...

            // Return a response (you may want to render a template or return JSON, depending on your needs)
            return $this->render('flights/add_flight.html.twig', [
                'form' => $form->createView(),
                'data' => json_encode($data_callendar),
                'obj' => $obj,
            ]);
        } else {
            // Handle the error, for example, return an error response
            return new Response('Error fetching data from the API', $response->getStatusCode());
        }
    }
}

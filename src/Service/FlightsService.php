<?php

namespace App\Service;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Flight;
use App\Entity\FlightPrices;
use Exception;

class FlightsService extends AbstractController
{

    public function searchFlight($departure, $destination, $targetDate)
    {   
            // Fetch data from the API
            $apiUrl = "https://www.ryanair.com/api/farfnd/v4/oneWayFares/{$departure}/{$destination}/cheapestPerDay?outboundMonthOfDate={$targetDate->format('Y-m-d')}&currency=PLN";
            $httpClient = HttpClient::create();
            $response = $httpClient->request('GET', $apiUrl);
            $data = $response->getContent();

            $obj = json_decode($data);

            // Check if the request was successful
            if ($response->getStatusCode() === 200) {              
                foreach($obj->outbound->fares as $fare) {
                    if($fare->unavailable == false) {
                        $arrivalTime =new DateTime($fare->arrivalDate);
                        $departureTime =new DateTime($fare->departureDate);
                        $data_callendar[] = array(
                            'title' => $departureTime->format('H:i') . '-' . $arrivalTime->format('H:i'),
                            'start' => $fare->day,
                        );
                        $data_callendar[] = array(
                            'title' => $fare->price->value . $fare->price->currencySymbol,
                            'start' => $fare->day,
                            'backgroundColor' => $this->getBackgroundColor($fare->price->value),
                            'overlap' => false,
                        );
                    }      
                }                
            } else {
                throw new Exception('failed to get data from API');
            }
            if (empty($data_callendar)) {
                $data_callendar = [];
            }

            return json_encode($data_callendar);
         
    }

    public function saveFlights(EntityManagerInterface $entityManager, $obj): void
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        date_default_timezone_set('Europe/Warsaw');

        foreach($obj->trips as $key => $trip) {
            $flightEntity = new Flight();
            $flightEntity->setFlightNumber($trip->dates[0]->flights[0]->flightNumber);
            $flightEntity->setDeparture($trip->origin);
            $flightEntity->setDepartureName($trip->originName);
            $flightEntity->setDestination($trip->destination);
            $flightEntity->setDestinationName($trip->destinationName);
            $flightEntity->setDateDepart(new DateTime($trip->dates[0]->flights[0]->time[0]));
            $flightEntity->setDateArriv(new DateTime($trip->dates[0]->flights[0]->time[1]));
            $flightEntity->setUserId($user->getId());

            $entityManager->persist($flightEntity);
            $entityManager->flush();

            $flightPriceEntity = new FlightPrices();
            $flightPriceEntity->setFlightId($flightEntity->getId());
            $flightPriceEntity->setPrice($trip->dates[0]->flights[0]->regularFare->fares[0]->amount);  
            $flightPriceEntity->setCurrency($obj->currency);
            $flightPriceEntity->setRecordedAt(new \DateTime());
            $flightPriceEntity->setUserId($user->getId());

            $entityManager->persist($flightPriceEntity);

            $flights[] = $flightEntity;

            if(count($obj->trips) > 1 && $key === 1) {
                // Link the return flight to the outbound flight return flight
                $flights[0]->setReturnFlight($flights[1]->getId());
                $flights[1]->setReturnFlight($flights[0]->getId());
    
                // Persist the updated flight entities
                $entityManager->persist($flights[0]);
                $entityManager->persist($flights[1]);
            }
        }
        $entityManager->flush();
        
    }

    public function getAirports() 
    {
        // Fetch data from the API
        $apiUrl = 'https://www.ryanair.com/api/views/locate/3/airports/en/active';
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', $apiUrl);
        $airportData = $response->toArray();

        // Filter only names with iataCode
        $airportNames = [];
        foreach ($airportData as $airport) {
            $airportNames[$airport['iataCode']] = $airport['name'];
        }

        return $airportNames;
    }

    public function getRouteAirports($airport) 
    {
        // Fetch data from the API
        $apiUrl = "https://www.ryanair.com/api/views/locate/searchWidget/routes/en/airport/{$airport}";
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', $apiUrl);
        $airportData = $response->toArray();

        $airportNames = [];
        foreach ($airportData as $airport) {
            $airportNames[$airport['arrivalAirport']['code']] = $airport['arrivalAirport']['name'];
        }

        return $airportNames;
    }

    private function getBackgroundColor($price)
    {
        if($price < 200) {
            return 'green';
        }
        elseif($price < 500) {
            return '#3788d8';
        } 
        else {
            return "orange";
        }
    }

}
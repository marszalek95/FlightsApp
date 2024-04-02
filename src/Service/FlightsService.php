<?php

namespace App\Service;

use DateTime;
use PHPUnit\Util\Color;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;

class FlightsService extends AbstractController
{

    public function searchFlight($departure, $destination)
    {   
        $currentDate = new DateTime('first day of this month');

        // Loop through the next 12 months
        for ($i = 0; $i < 12; $i++) {
            // Calculate the date for the current iteration
            $targetDate = clone $currentDate;
            $targetDate->modify("+$i months");

            // Fetch data from the API
            $apiUrl = "https://www.ryanair.com/api/farfnd/v4/oneWayFares/{$departure}/{$destination}/cheapestPerDay?outboundMonthOfDate={$targetDate->format('Y-m-d')}&currency=PLN";
            $httpClient = HttpClient::create();
            $response = $httpClient->request('GET', $apiUrl);
            $data = $response->getContent();

            $obj = json_decode($data);

            // Check if the request was successful (status code 200)
            if ($response->getStatusCode() === 200) {
                // Decode the JSON response
                
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

                // Filter the data and merge with the existing results
                
            } else {
                // Handle the case when the API request fails
                // ...
            }
        }

            // Return a response
            return json_encode($data_callendar);
         
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
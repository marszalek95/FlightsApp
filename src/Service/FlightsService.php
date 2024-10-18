<?php

namespace App\Service;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Flight;
use App\Entity\FlightPrices;
use DateTimeZone;
use Exception;

class FlightsService extends AbstractController
{

    public function searchFlight($departure, $destination, $targetDate)
    {   
            // Fetch data from the API
            $apiUrl = "https://www.ryanair.com/api/farfnd/v4/oneWayFares/{$departure}/{$destination}/cheapestPerDay?outboundMonthOfDate={$targetDate->format('Y-m-d')}&currency=PLN";
            $httpClient = HttpClient::create();
            $response = $httpClient->request('GET', $apiUrl);

            // Check if the request was successful
            if ($response->getStatusCode() === 200) {
                $data = $response->getContent();

                $obj = json_decode($data);              
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

    public function getFlights($departure, $destination, $dateout, $datein)
    {
        $apiUrl = "https://www.ryanair.com/api/booking/v4/en-gb/availability?ADT=1&TEEN=0&CHD=0&INF=0&Origin=$departure&Destination=$destination&promoCode=&IncludeConnectingFlights=false&DateOut=$dateout&DateIn=$datein&FlexDaysBeforeOut=2&FlexDaysOut=2&FlexDaysBeforeIn=2&FlexDaysIn=2&RoundTrip=true&ToUs=AGREED";

        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', $apiUrl, [
            'headers' => [
                'Host' => 'www.ryanair.com',
                'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64; rv:126.0) Gecko/20100101 Firefox/126.0',
                'Accept-Language' => 'en-US,en;q=0.5',
                'Upgrade-Insecure-Requests' => '1',
                'Sec-Fetch-Dest' => 'document',
                'Sec-Fetch-Mode' => 'navigate',
                'Sec-Fetch-Site' => 'cross-site',
                'Connection' => 'keep-alive',
                'Alt-Used' => 'www.ryanair.com',
                'Cookie' => 'rid=75ffd81e-a3e0-4445-bde2-63207dcaf3f8; rid.sig=jyna6R42wntYgoTpqvxHMK7H+KyM6xLed+9I3KsvYZaVt7P36AL6zp9dGFPu5uVxaIiFpNXrszr+LfNCdY3IT3oCSYLeNv/ujtjsDqOzkY66AL3V6kH2vsK+au12X21HkZ4S8GaG8CoBmm/m0rLsOKYkxtw+U3+ejBaPc15jJjLYtVUOp3BidYIUddjdDtnvimtzCw7UNncUrHjiDJKFf2RnvHajd1wp4bHDCcLyMDPaD3g+IFRLPtaGcFbtwKdRYVTUlBzWsK4cwM4n9ojCWKArIsV9S5xkJPtz3w8WGjs1s5/g2QfS6TvOew66H0UfMsLhV7DZzwJKDTsWm8AvYCr5IKJ+ub1ykpRNVXed3KDRr7ffkkyHs64cf4V2tQYNXQjWCZZFqR98dfmZJ99ACAmP9r/lHEklLL0D0bpe+drkl/0FrsM/3g5mWJRlkti79108p7Mt5+MwCZK4kSNqwh0CUTPyQSevc4BxKBjSsNkagg1z7yKsRhB+hSSID5ehDRooyLceZOYZpooXSjwue9hCvvNbEnWFpBZDiyCwCzouZun3plq9mPgR3dhJ61kmPCuqZiwL1yPkGtG1n8Bb3M7rQ+p58KE3oqij1GPdIfM2t4gQj8z0NWBEF4EMwaOsmCFcd6/pG4f7OobZNPeS63WKreDBoDKsvo+8BbEGQ8WnM1w6Ty7nh4PhbradOprxKIdLMfjzTe5XPNlEIzXoM68JRprbDbgQc/9S4VidtKzRaDzC+1TDxMAA6uKBFbNnYW74HG+wf6iyNfXklFDHPVOiwf0MC0c2p33ysZgDJYvKgRBifPepP0RTXFWhhQ50VY+3lum94RCVjzjmhmiUjgu0lc2aOMpfASix/hznc2CRl/mJXgCFFCxfLwB/J3shuLyUgxS+msLqTdYeKAxkbIqIKPM6XdbF61eYRt8r+DJEQ4Bo9J+H4yFwyNirF6JJ8Rw5csOEWx6A8Lgo3oj0k6HcielxXgHvtFx0vo0SFam6eV8dmZzy5vfgY4/rxirwavrq7uLbGwFbpnnx+azVplIdTGxhMZiiHI4h16OhagTPVTcgw52N9aC0Hs+ZDCKR; mkt=/ie/en/; _ga=GA1.2.692581857.1702899056; bid_FRwdAp7a9G2cnLnTsgyBNeduseKcPcRy=45b7ede0-f2c4-4e2c-91cf-6a8c742ef5b5; _hjSessionUser_135144=eyJpZCI6ImQ3YWQ0NGE1LTkwODgtNWFiMy1iNjc0LTEwNWE1MWIzMTVmNiIsImNyZWF0ZWQiOjE3MDI4OTkwNTkwMjMsImV4aXN0aW5nIjp0cnVlfQ==; _ga_C5LQBBVHC8=GS1.1.1706216019.1.0.1706216022.0.0.0; agsd=14Xvaa4VHYsZH7hAi-xDLFjaesLgGKWni9krfQzceZ3Dw4Cl; _cc=ATg0IiWcigovHQAzgf89GJeJ; _cid_cc=ATg0IiWcigovHQAzgf89GJeJ; sid=45b7ede0-f2c4-4e2c-91cf-6a8c742ef5b5; STORAGE_PREFERENCES={"STRICTLY_NECESSARY":true,"PERFORMANCE":true,"FUNCTIONAL":true,"TARGETING":true,"SOCIAL_MEDIA":true,"PIXEL":true,"GANALYTICS":true,"__VERSION":2}; RY_COOKIE_CONSENT=true; fr-correlation-id=d6f79e8e-8525-4e0a-8651-0cc3e79c3c3f; agso=AXEEKeYBABQJua87JNxIgvp8IGYZpmU.; agsn=4LeywE-3EoiYrS5b4KHuHhxnLWu2G0gE-NuzqROxsjI.; agssn=ARtioCwBAPrqIA5RT9xI5usYtQ..; myRyanairID=; .AspNetCore.Session=CfDJ8LC1%2FeDyHD1FpHnh%2B0IW8XrhqILeDo8H5JYXwYAJYUMnIgW5IDcg%2Fu3geSmisob6azWzXBNeMmyFfQaRvgo0jqiAUgk0s4skIcUM%2FiEOq3IXHwwsxEE9UNauFuh%2BqOZWRJUXJV%2FAdWOTsqCx0Gs7GPlfOdRARH8OrSMWRiq1oIRw',
                'Priority' => 'u=1',
            ],
        ]);

        if ($response->getStatusCode() === 200) {
            $data = $response->getContent();

            $obj = json_decode($data);
        } else {
            throw new Exception('failed to get data from API');
        }

        return $obj;     
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
            $flightEntity->setFlightKey($trip->dates[0]->flights[0]->flightKey);

            $entityManager->persist($flightEntity);
            $entityManager->flush();

            $flightPriceEntity = new FlightPrices();
            $flightPriceEntity->setFlightId($flightEntity->getId());
            $flightPriceEntity->setPrice($trip->dates[0]->flights[0]->regularFare->fares[0]->amount);  
            $flightPriceEntity->setCurrency($obj->currency);
            $flightPriceEntity->setRecordedAt(new DateTime('now', new DateTimeZone('UTC')));
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
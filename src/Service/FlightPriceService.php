<?php

namespace App\Service;

use App\Entity\Flight;
use App\Entity\FlightPrices;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\FlightsService;
use DateTimeZone;
use DateTime;

class FlightPriceService
{
    private $entityManager;
    private $flightsService;

    public function __construct(EntityManagerInterface $entityManager, FlightsService $flightsService)
    {
        $this->entityManager = $entityManager;
        $this->flightsService = $flightsService;
    }

    public function updateFlightPrices()
    {
        $savedFlights = $this->entityManager->getRepository(Flight::class)->findAll();

        $processedReturnFlights = [];

        foreach ($savedFlights as $savedFlight) {
            // Ensure the return flight hasn't already been processed
            if (in_array($savedFlight->getId(), $processedReturnFlights)) {
                continue;
            }
            // Check if this flight has a return flight
            if ($savedFlight->getReturnFlight()) {
                $returnFlight = $this->entityManager->getRepository(Flight::class)->find($savedFlight->getReturnFlight());
                $compareFlight = $this->flightsService->getFlights($savedFlight->getDeparture(), $savedFlight->getDestination(), $savedFlight->getDateDepart()->format('Y-m-d'), $returnFlight->getDateDepart()->format('Y-m-d'));

                $price = $this->entityManager->getRepository(FlightPrices::class)->findPrice($savedFlight->getId());
                $priceReturn = $this->entityManager->getRepository(FlightPrices::class)->findPrice($returnFlight->getId());


                foreach ($compareFlight->trips[0]->dates as $date) {
                    foreach ($date->flights as $flight) {
                        if ($flight->flightKey === $savedFlight->getFlightKey()) {
                            $comparePrice = $flight->regularFare->fares[0]->amount;
                            if ($price->getPrice() !== $comparePrice) {
                                $flightPriceEntity = new FlightPrices();
                                $flightPriceEntity->setFlightId($price->getFlightId());
                                $flightPriceEntity->setPrice($comparePrice);  
                                $flightPriceEntity->setCurrency($price->getCurrency());
                                $flightPriceEntity->setRecordedAt(new \DateTime());
                                $flightPriceEntity->setUserId($price->getUserId());

                                $this->entityManager->persist($flightPriceEntity);
                                $this->entityManager->flush();
                            }
                            break 2;
                        }
                    }
                }

                foreach ($compareFlight->trips[1]->dates as $date) {
                    foreach ($date->flights as $flight) {
                        if ($flight->flightKey === $returnFlight->getFlightKey()) {
                            $comparePrice = $flight->regularFare->fares[0]->amount;
                            if ($priceReturn->getPrice() !== $comparePrice) {
                                $flightPriceEntity = new FlightPrices();
                                $flightPriceEntity->setFlightId($priceReturn->getFlightId());
                                $flightPriceEntity->setPrice($comparePrice);  
                                $flightPriceEntity->setCurrency($priceReturn->getCurrency());
                                $flightPriceEntity->setRecordedAt(new \DateTime());
                                $flightPriceEntity->setUserId($priceReturn->getUserId());

                                $this->entityManager->persist($flightPriceEntity);
                                $this->entityManager->flush();
                            }
                            break 2;
                        }
                    }
                }
                
                // Mark the return flight as processed
                $processedReturnFlights[] = $returnFlight->getId();
                
            } else {
                $compareFlight = $this->flightsService->getFlights($savedFlight->getDeparture(), $savedFlight->getDestination(), $savedFlight->getDateDepart()->format('Y-m-d'), NULL);

                $price = $this->entityManager->getRepository(FlightPrices::class)->findPrice($savedFlight->getId());

                foreach ($compareFlight->trips[0]->dates as $date) {
                    foreach ($date->flights as $flight) {
                        if ($flight->flightKey === $savedFlight->getFlightKey()) {
                            $comparePrice = $flight->regularFare->fares[0]->amount;
                            if ($price->getPrice() !== $comparePrice) {
                                $flightPriceEntity = new FlightPrices();
                                $flightPriceEntity->setFlightId($price->getFlightId());
                                $flightPriceEntity->setPrice($comparePrice);  
                                $flightPriceEntity->setCurrency($price->getCurrency());
                                $flightPriceEntity->setRecordedAt(new DateTime('now', new DateTimeZone('UTC')));
                                $flightPriceEntity->setUserId($price->getUserId());

                                $this->entityManager->persist($flightPriceEntity);
                                $this->entityManager->flush();
                            }
                            break 2;
                        }
                    }
                }
            }
        }




        // $flightPrices = $this->entityManager->getRepository(FlightPrices::class)->findPrices();


        // foreach ($flightPrices as $flightPrice) {
        //     // $flightEntity = $this->entityManager->getRepository(Flight::class)->find($flightPrice->getFlightId());
        //     // $flight = $this->flightsService->getFlights($flightEntity->getDeparture(), $flightEntity->getDestination(), $flightEntity->getDateDepart()->format('Y-m-d'), NULL);
            
        //     // foreach ($flight->trips[0]->dates as $date) {
        //     //     foreach ($date->flights as $flight) {
        //     //         if ($flight->flightKey === $flightEntity->getFlightKey()) {
        //     //             $price = $flight->regularFare->fares[0]->amount;
        //     //             if ($price !== $flightPrice->getPrice()) {
        //     //                 $flightPriceEntity = new FlightPrices();
        //     //                 $flightPriceEntity->setFlightId($flightPrice->getFlightId());
        //     //                 $flightPriceEntity->setPrice($price);  
        //     //                 $flightPriceEntity->setCurrency($flightPrice->getCurrency());
        //     //                 $flightPriceEntity->setRecordedAt(new \DateTime());
        //     //                 $flightPriceEntity->setUserId($flightPrice->getUserId());

        //     //                 $this->entityManager->persist($flightPriceEntity);
        //     //                 $this->entityManager->flush();
        //     //             }
        //     //             break 2;
        //     //         }
        //     //     }
        //     // }
        // }
        echo 'cron update';
        
    }
}

<?php

namespace App\Twig\Components;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use App\Entity\Flight;
use App\Entity\FlightPrices;

#[AsLiveComponent]
class DeleteFlight extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp]
    public int $id;

    #[LiveAction]
    public function deleteFlight(#[LiveArg] int $id, #[LiveArg] EntityManagerInterface $entityManager)
    {
        $flight = $entityManager->getRepository(Flight::class)->find($id);
        $prices = $entityManager->getRepository(FlightPrices::class)->findby(['flight_id' => $id]);
        $entityManager->remove($flight);

        foreach ($prices as $price) {
            $entityManager->remove($price);
        }

        if (null !== ($flight->getReturnFlight())) {
            $flightReturn = $entityManager->getRepository(Flight::class)->find($flight->getReturnFlight());
            $pricesReturn = $entityManager->getRepository(FlightPrices::class)->findby(['flight_id' => $flight->getReturnFlight()]);
            $entityManager->remove($flightReturn);

            foreach ($pricesReturn as $priceReturn) {
                $entityManager->remove($priceReturn);
            }
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_flights');

    }

}

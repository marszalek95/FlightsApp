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

    #[LiveProp]
    public int $id_return;

    #[LiveAction]
    public function deleteFlight(#[LiveArg] int $id, #[LiveArg] int $idReturn, EntityManagerInterface $entityManager)
    {

        $flight = $entityManager->getRepository(Flight::class)->find($id);
        $flightReturn = $entityManager->getRepository(Flight::class)->find($idReturn);
        $prices = $entityManager->getRepository(FlightPrices::class)->findby(['flight_id' => $id]);
        $pricesReturn = $entityManager->getRepository(FlightPrices::class)->findby(['flight_id' => $idReturn]);

        $entityManager->remove($flight);
        $entityManager->remove($flightReturn);
        foreach ($prices as $price) {
            $entityManager->remove($price);
        }
        foreach ($pricesReturn as $priceReturn) {
            $entityManager->remove($priceReturn);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_flights');

    }

}

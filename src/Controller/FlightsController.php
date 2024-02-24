<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

class FlightsController extends AbstractController
{
    #[Route('/flights', name: 'app_flights')]
    public function index(): Response
    {
        return $this->render('flights/index.html.twig', [
            
        ]);
    }

    #[Route('/addflight', name: 'app_addflight')]
    public function addFlight(): Response
    {
        return $this->render('flights/add_flight.html.twig', [
        ]);
    }
}

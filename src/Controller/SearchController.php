<?php

namespace App\Controller;

use App\Form\SearchFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpClient\HttpClient;


class SearchController extends AbstractController
{
    #[Route('/addflight', name: 'app_addflight')]
    public function search(Request $request): Response
    {
        $form = $this->createForm(SearchFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            

            if ($form->isSubmitted() && $form->isValid()) {
                
                $departure = $form->getData()['departure'];
                $destination = $form->getData()['destination'];
                return $this->redirect("/addflight/search/{$departure}/{$destination}");
            }
    }

        return $this->render('flights/add_flight.html.twig', [
            'form' => $form->createView(),
            'data' => null,
        ]);
    }
}
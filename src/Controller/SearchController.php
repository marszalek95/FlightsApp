<?php

namespace App\Controller;

use App\Form\SaveFormType;
use App\Form\SearchFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;



class SearchController extends AbstractController
{
    #[Route('/addflight', name: 'app_addflight')]
    public function search(Request $request): Response
    {
        $form = $this->createForm(SearchFormType::class);
        $saveform = $this->createForm(SaveFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $type = $form->getData()['type'];
            $departure = $form->getData()['departure'];
            $destination = $form->getData()['destination'];   
            return $this->redirect("/addflight/search/{$departure}/{$destination}/{$type}");
        }

        return $this->render('flights/add_flight.html.twig', [
            'form' => $form,
            'saveform' => $saveform,
            'type' => null,
            'departure' => null,
            'destination' => null
        ]);
    }
}
<?php

namespace App\Twig\Components;

use App\Form\SaveFormType;
use App\Service\FlightsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\Component\HttpClient\HttpClient;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[AsLiveComponent(csrf: false)]
class SaveFlights extends AbstractController
{

    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public $type;

    #[LiveProp]
    public $departure;

    private $departureName;

    #[LiveProp]
    public $destination;

    private $destinationName;

    private $flightService;

    public function __construct(FlightsService $flightService) {
        $this->flightService = $flightService;
    }

    #[LiveAction]
    public function addDeparture(#[LiveArg('departure')] string $departureselect)
    {
        $this->formValues['departureselect'] = $departureselect;
    }

    #[LiveAction]
    public function addDestination(#[LiveArg('destination')] string $destinationselect)
    {
        $this->formValues['destinationselect'] = $destinationselect;
    }

    #[LiveAction]
    public function fetchFlights(EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $departure = $this->departure;
        $destination = $this->destination;
        $dateout = new DateTime($this->formValues['departureselect']);
        $datein = new DateTime($this->formValues['destinationselect']);
        $dateout = $dateout->format('Y-m-d\TH:i:s.000');
        $datein = $datein->format('Y-m-d\TH:i:s.000');

        $obj = $this->flightService->getFlights($departure, $destination, $dateout, $datein);

        foreach ($obj->trips[0]->dates as $key => $date) {
            if($date->dateOut != $dateout) {
                unset($obj->trips[0]->dates[$key]);
            } else {
                foreach($date->flights as $key => $flight) {
                    if($flight->faresLeft == 0) {
                        unset($obj->trips[0]->dates->flights[$key]);
                    }
                }
            }
        }

        foreach ($obj->trips[1]->dates as $key => $date) {
            if($date->dateOut != $datein) {
                unset($obj->trips[1]->dates[$key]);
            } else {
                foreach($date->flights as $key => $flight) {
                    if($flight->faresLeft == 0) {
                        unset($obj->trips[1]->dates->flights[$key]);
                    }
                }
            }
        }

        sort($obj->trips[0]->dates);
        sort($obj->trips[1]->dates);
        
        if (count($obj->trips[0]->dates[0]->flights) > 1 || count($obj->trips[1]->dates[0]->flights) > 1) {
            // If there are multiple trips, render the selection page
            $session->set('flights', $obj);
            return $this->redirectToRoute('app_save_flight');
        } else {
            sort($obj->trips[0]->dates[0]->flights);
            sort($obj->trips[1]->dates[0]->flights);
            $this->flightService->saveFlights($entityManager, $obj);
            return $this->redirectToRoute('app_flights');
        }
    }

    #[LiveAction]
    public function fetchFlight(EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $departure = $this->departure;
        $destination = $this->destination;
        $dateout = new DateTime($this->formValues['departureselect']);
        $dateout = $dateout->format('Y-m-d\TH:i:s.000');

        $obj = $this->flightService->getFlights($departure, $destination, $dateout, NULL);

        foreach ($obj->trips[0]->dates as $key => $date) {
            if($date->dateOut != $dateout) {
                unset($obj->trips[0]->dates[$key]);
            } else {
                foreach($date->flights as $key => $flight) {
                    if($flight->faresLeft == 0) {
                        unset($obj->trips[0]->dates->flights[$key]);
                    }
                }
            }
        }

        sort($obj->trips[0]->dates);

        if (count($obj->trips[0]->dates[0]->flights) > 1) {
            // If there are multiple trips, render the selection page
            $session->set('flights', $obj);
            return $this->redirectToRoute('app_save_flight');
        } else {
            sort($obj->trips[0]->dates[0]->flights);
            $this->flightService->saveFlights($entityManager, $obj);
            return $this->redirectToRoute('app_flights');
        }
    }
    
    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(SaveFormType::class);
    }
}

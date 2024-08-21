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
use Psr\Log\LoggerInterface;
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
    public function fetchFlights(EntityManagerInterface $entityManager, LoggerInterface $logger, SessionInterface $session)
    {
        $departure = $this->departure;
        $destination = $this->destination;
        $dateout = new DateTime($this->formValues['departureselect']);
        $datein = new DateTime($this->formValues['destinationselect']);
        $dateout = $dateout->format('Y-m-d\TH:i:s.000');
        $datein = $datein->format('Y-m-d\TH:i:s.000');


        $url = "https://www.ryanair.com/api/booking/v4/en-gb/availability?ADT=1&TEEN=0&CHD=0&INF=0&Origin=$departure&Destination=$destination&promoCode=&IncludeConnectingFlights=false&DateOut=$dateout&DateIn=$datein&FlexDaysBeforeOut=2&FlexDaysOut=2&FlexDaysBeforeIn=2&FlexDaysIn=2&RoundTrip=true&ToUs=AGREED";

        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', $url, [
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
        // $logger->error($dateout->format('Y-m-d\TH:i:s.v'));

        if ($response->getStatusCode() === 200) {
            $data = $response->getContent();
            $obj = json_decode($data);

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
            


            $logger->error(count($obj->trips[0]->dates[0]->flights));

            // var_dump($obj);

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

        } else {
            throw new \Exception('Failed to fetch data from API');
        }
    }

    #[LiveAction]
    public function fetchFlight(EntityManagerInterface $entityManager, LoggerInterface $logger, SessionInterface $session)
    {
        $departure = $this->departure;
        $destination = $this->destination;
        $dateout = new DateTime($this->formValues['departureselect']);
        $dateout = $dateout->format('Y-m-d\TH:i:s.000');

        $url = "https://www.ryanair.com/api/booking/v4/en-gb/availability?ADT=1&TEEN=0&CHD=0&INF=0&Origin=$departure&Destination=$destination&promoCode=&IncludeConnectingFlights=false&DateOut=$dateout&DateIn=&FlexDaysBeforeOut=2&FlexDaysOut=2&FlexDaysBeforeIn=2&FlexDaysIn=2&RoundTrip=true&ToUs=AGREED";

        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', $url, [
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
        // $logger->error($dateout->format('Y-m-d\TH:i:s.v'));

        if ($response->getStatusCode() === 200) {
            $data = $response->getContent();
            $obj = json_decode($data);

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

        } else {
            throw new \Exception('Failed to fetch data from API');
        }
    }
    
    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(SaveFormType::class);
    }
}

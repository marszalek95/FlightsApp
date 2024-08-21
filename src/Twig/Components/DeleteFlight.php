<?php

namespace App\Twig\Components;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;

#[AsLiveComponent]
class DeleteFlight extends AbstractController
{
    use DefaultActionTrait;

    #[LiveAction]
    public function deleteFlight()
    {
        return $this->redirectToRoute('app_flights');

    }

}

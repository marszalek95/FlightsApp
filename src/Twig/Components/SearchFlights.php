<?php

namespace App\Twig\Components;

use App\Form\SearchFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;

#[AsLiveComponent]
class SearchFlights extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveAction]
    public function swap()
    {
        $departure = $this->formValues['departure'];
        $destination = $this->formValues['destination'];
        $this->formValues['departure'] = $destination;
        $this->formValues['destination'] = $departure;

    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(SearchFormType::class);
    }
}

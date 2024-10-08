<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfonycasts\DynamicForms\DependentField;
use Symfonycasts\DynamicForms\DynamicFormBuilder;
use App\Service\FlightsService;

class SearchFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $flightService = new FlightsService();
        $airportNames = $flightService->getAirports();

        $builder = new DynamicFormBuilder($builder);

        $builder
            ->add('departure', ChoiceType::class, [     
                'choices' => array_flip($airportNames),
                'placeholder' => 'Departure',
                'data' => $options['departure'],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('search', SubmitType::class, [
                'label' => 'Search',
                'attr' => ['class' => 'btn btn-primary'],
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Return trip' => 'return',
                    'One way' => 'oneWay',
                ],
                'expanded' => true,
                'multiple' => false,
                'data' => $options['actualtype'],
                'choice_attr' => function ($choice, $key, $value) {
                    // Add Bootstrap classes to the radio buttons
                    return ['class' => 'form-check-input'];
                },
            ])
            ->addDependent('destination', ['departure'], function(DependentField $field, ?string $airport) use ($options) {
                
                if(isset($airport)) {
                    $flightService = new FlightsService();
                    $departureAirportNamestest = $flightService->getRouteAirports($airport);
                }
                else {
                    $departureAirportNamestest = ['Select departure airport.'];
                }

                $field->add(ChoiceType::class, [
                    'choices' => array_flip($departureAirportNamestest),
                    'placeholder' => 'Destination',
                    'data' => $options['destination'],
                    'required' => true,
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ]);
            });         
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'departure' => null,
            'destination' => null,
            'actualtype' => 'return',
        ]);
    }
}

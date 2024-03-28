<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $airportNames = [
            'POZ' => 'Poznan',
            'WAW' => 'Warsaw',
            'LIS' => 'Lisbon',
        ];

        $builder
            ->add('departure', ChoiceType::class, [     
                'choices' => array_flip($airportNames),
                'placeholder' => 'Departure',
                'data' => $options['departure'],
                'attr' => [
                    'class' => 'form-control',       
                ],
            ])
            ->add('destination', ChoiceType::class, [
                'choices' => array_flip($airportNames),
                'placeholder' => 'Destination',
                'data' => $options['destination'],
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
            ]);
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

<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('departure', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Departure',
                    'value' => $options['departure'],
                ],
            ])
            ->add('destination', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Destination',
                    'value' => $options['destination'],
                ],
            ])
            ->add('search', SubmitType::class, [
                'label' => 'Search',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'departure' => null,
            'destination' => null,
        ]);
    }
}

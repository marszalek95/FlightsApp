<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SaveFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        
        $builder
            ->add('departureselect', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => '',
                ],
            ])
            ->add('destinationselect', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => '',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => [
                    'class' => 'btn btn-primary',
                    'data-action' => 'live#action:prevent',
                    'data-live-action-param' => 'fetchFlights'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

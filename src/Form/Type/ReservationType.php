<?php

namespace App\Form\Type;

use App\Entity\Reservation;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('timeFrom', DateTimeType::class, ['label' => 'Rezervovat od'])
            ->add('timeTo', DateTimeType::class, ['label' => 'Rezervovat do'])
            ->add('invitedUsers', ChoiceType::class, [
                'label' => 'Pozvat další uživatele',
                'required' => false,
                'choices' => $options['choices'],
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('save', SubmitType::class, ['label' => 'Vytvořit rezervaci']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'choices' => [],
        ]);
    }
}
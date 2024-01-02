<?php

namespace App\Form\Type;

use App\Entity\Reservation;
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
        if ($options['isManager']) {
            $builder
                ->add('responsibleUser', ChoiceType::class, [
                    'label' => 'Rezervovat na jméno',
                    'choices' => $options['responsibleChoices'],
                    'expanded' => false,
                    'multiple' => false,
                ]);
        }

        if ($options['actionType'] === 'new') {
            $builder
                ->add('timeFrom', DateTimeType::class, ['label' => 'Rezervovat od'])
                ->add('timeTo', DateTimeType::class, ['label' => 'Rezervovat do']);
        }

        $builder
            ->add('invitedUsers', ChoiceType::class, [
                'label' => 'Pozvaní uživatelé',
                'required' => false,
                'choices' => $options['inviteChoices'],
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('save', SubmitType::class, [
                'label' => $this->submitLabel($options),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'actionType' => 'new',
            'isManager' => false,
            'responsibleChoices' => [],
            'inviteChoices' => [],
        ]);
    }

    private function submitLabel($options): string
    {
        if ($options['actionType'] === 'edit') {
            return 'Uložit změny';
        } elseif ($options['isManager']) {
            return 'Vytvořit a schválit rezervaci';
        } else {
            return 'Odeslat žádost o rezervaci';
        }
    }
}
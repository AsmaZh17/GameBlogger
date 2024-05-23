<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\Requirement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('image')
            ->add('prix')
            ->add('solde')
            ->add('description' , null, ['label' => false])
            ->add('minimumRequirements', EntityType::class, [
                'class' => Requirement::class,
                'choice_label' => 'id',
            ])
            ->add('recommendedRequirements', EntityType::class, [
                'class' => Requirement::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Requirement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequirementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('os')
            ->add('processor')
            ->add('memory')
            ->add('graphics')
            ->add('directX')
            ->add('network')
            ->add('hardDrive')
            ->add('soundCard')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Requirement::class,
        ]);
    }
}

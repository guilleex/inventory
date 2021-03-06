<?php

namespace AppBundle\Form;

use AppBundle\Entity\OutValue;
use AppBundle\Entity\Machine;
use AppBundle\Repository\MachineRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class OutValueFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('machine', EntityType::class, [
                'placeholder' => 'Select a Machine',
                'class' => Machine::class,
                'query_builder' => function(MachineRepository $repo) {
                    return $repo->createAlphabeticalQueryBuilder();
                }
            ])
            ->add('value', null, [
                'label' => false,
                'attr' => ['class' => 'submitreplacement']
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text'
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OutValue::class
        ]);
    }


}

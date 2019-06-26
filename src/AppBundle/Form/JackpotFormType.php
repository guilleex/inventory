<?php

namespace AppBundle\Form;

use AppBundle\Entity\Jackpot;
use AppBundle\Entity\MachineType;
use AppBundle\Repository\MachineTypeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class JackpotFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('machineType', EntityType::class, [
                'placeholder' => 'Select a Machine Type',
                'class' => MachineType::class,
                'query_builder' => function(MachineTypeRepository $repo) {
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
            'data_class' => Jackpot::class
        ]);
    }

}

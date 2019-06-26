<?php

namespace AppBundle\Form;

use AppBundle\Entity\Machine;
use AppBundle\Entity\MachineType;
use AppBundle\Entity\InValue;
use AppBundle\Repository\MachineTypeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class MachineFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', EntityType::class, [
                'placeholder' => 'Select a Machine Type',
                'class' => MachineType::class,
                'query_builder' => function(MachineTypeRepository $repo) {
                    return $repo->createAlphabeticalQueryBuilder();
                }
            ])
            ->add('name')
            ->add('ratio', ChoiceType::class, [
                'choices' => [
                    '1'  => 1,
                    '5'  => 5,
                    '10' => 10,
                    ]
            ])
            ->add('position')
            ->add('visible')
            ->add('inValues', CollectionType::class, [
                'entry_type' => InValueFormType::class,
                'allow_delete' => true,
                'allow_add' => true,
                'by_reference' => false,
            ])
            ->add('outValues', CollectionType::class, [
                'entry_type' => OutValueFormType::class,
                'allow_delete' => true,
                'allow_add' => true,
                'by_reference' => false,
            ])
            ->addEventListener(
                FormEvents::POST_SET_DATA,
                array($this, 'onPostSetData')
            )
        ;
    }

    public function onPostSetData(FormEvent $event)
    {
        if ($event->getData() && $event->getData()->getId()) {
            $form = $event->getForm();
            unset($form['inValues']);
            unset($form['outValues']);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Machine::class
        ]);
    }
}

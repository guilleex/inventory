<?php

namespace AppBundle\Form;

use AppBundle\Entity\Worker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class WorkerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'help' => 'Please enter worker name.'
            ])
//            ->add('incomeInputs', CollectionType::class, [
//                'entry_type' => IncomeInputFormType::class,
//                'allow_add' => true,
//                'allow_delete' => true,
//                'by_reference' => false
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Worker::class
        ]);
    }

}

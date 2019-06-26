<?php

namespace AppBundle\Form;

use AppBundle\Entity\Income;
use AppBundle\Entity\Worker;
use AppBundle\Entity\IncomeInput;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class IncomeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'help' => 'Please enter month'
            ])
            ->add('value')
            ->add('workers', CollectionType::class, [
                'entry_type' => WorkerFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])

        ;
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Income::class
        ]);
    }
    /**
     * @param FormEvent $event
     */
    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $data['workers'] = array_values($data['workers']);
        $event->setData($data);
    }
}
<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FamilyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('endDate',  'date')
            ->add('description',  'ckeditor', [
                'required'=>false,
                'label'=>'Description'
                ])
            ->add('maxVotes',  'integer', [
                'required'=>false
                ])
            ->add('slackChannel', 'text', [
                'required'=>false
                ])
            ->add('save', 'submit', array(
               'label' => 'Create',
               'attr'  => array(
                   'class' => 'btn btn-success',
               )))
        ;
    }

    public function getName()
    {
        return 'family';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Family',
        ));
    }
}

<?php

namespace AppBundle\Form\Type;

use AppBundle\Repository\FamilyRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('family', 'entity', array(
                'label' => 'Family',
                'class' => 'AppBundle\Entity\Family',
                'property' => 'name',
                'query_builder' => function(FamilyRepository $repository) {
                    return $repository->createQueryBuilder('f')
                        ->where('f.active = 1')
                        ->orderBy('f.name', 'ASC');
                },
            ))
            ->add('title', 'text')
            ->add('imageFile', 'vich_file', array(
                'required' => false,
                'allow_delete' => true, // not mandatory, default is true
                'download_link' => true, // not mandatory, default is true
            ))
            ->add('shortDescription', 'ckeditor')
            ->add('endDate',  'date')
            ->add('videoUrl', 'text', ['required'=>false])
            ->add('save', 'submit', array(
               'label' => 'Create',
               'attr'  => array(
                   'class' => 'btn btn-success',
               )))
        ;
    }

    public function getName()
    {
        return 'project';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Project',
        ));
    }
}

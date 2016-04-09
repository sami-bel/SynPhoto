<?php

namespace MainBundle\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre')
            ->add('album', EntityType::class, array(
                'class' => 'MainBundle:Album',
                'choice_label' => 'titre',
                'mapped' =>false,
                'multiple' =>true
            ))
            ->add('image',FileType::class, array('label' => 'image','mapped' =>false));
    }
}

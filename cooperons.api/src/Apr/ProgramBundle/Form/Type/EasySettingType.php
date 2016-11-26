<?php

namespace Apr\ProgramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EasySettingType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        $builder
            ->add('simpleRate', TextType::class)
            ->add('multiRate', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver) {
       $resolver->setDefaults(array(
            'data_class' => 'Apr\ProgramBundle\Entity\EasySetting'
        ));
    }

}

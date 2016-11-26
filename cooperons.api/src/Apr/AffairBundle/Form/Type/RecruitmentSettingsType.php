<?php

/**
* @author Fondative <devteam@fondative.com> 
 * 
* @ New RecruitmentSettings FormType 
*/

namespace Apr\AffairBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RecruitmentSettingsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('range1', TextType::class)
            ->add('range2', TextType::class)
            ->add('rate1', TextType::class)
            ->add('rate2', TextType::class)
            ->add('rateBeyond', TextType::class)
            ->add('duration', TextType::class);
    } 

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Apr\AutoEntrepreneurBundle\Entity\RecruitmentSettings'
        ));
    }
}

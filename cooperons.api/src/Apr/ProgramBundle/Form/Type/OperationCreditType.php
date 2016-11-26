<?php

namespace Apr\ProgramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class OperationCreditType extends AbstractType {

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('label', TextType::class)
                ->add('defaultAmount', NumberType::class)
                ->add('multi', CheckboxType::class)
                ->add('description', TextType::class);
    } 

    public function configureOptions(OptionsResolver $resolver) {
         $resolver->setDefaults(array(
            'data_class' => 'Apr\ProgramBundle\Entity\OperationCredit'
        ));
    }
}

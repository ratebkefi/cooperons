<?php

namespace Apr\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

/**
 * UserType : formulaire de création d'un nouveau utilisateur
 *
 * @author Mohammed Rhamnia <mr@edatis.com>
 */
class UserType extends AbstractType
{

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        $builder
            ->add('lastName', TextType::class)
           ->add('firstName', TextType::class)
            ->add('email', EmailType::class)
            ->add('plainPassword', 'repeated',  array('type' => 'password'));
    }

    public function configureOptions(OptionsResolver $resolver) {
       $resolver->setDefaults(array(
            'data_class' => 'Apr\UserBundle\Entity\User'
        ));
    }
}

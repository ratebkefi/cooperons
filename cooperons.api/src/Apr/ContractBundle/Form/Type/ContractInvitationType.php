<?php

namespace Apr\ContractBundle\Form\Type;

use Apr\UserBundle\Form\Type\InvitationBaseType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ContractInvitationType extends AbstractType
{

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('invitationType', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Apr\ContractBundle\Entity\ContractInvitation'
        ));
    }

    public function getParent()
    {
        return InvitationBaseType::class;
    }
}

<?php

/**
 * @author Fondative <devteam@fondative.com>
 *
 * @ New FromType to remove the check of password in user's account
 */

namespace Apr\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UserEditType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*
         * Remove repeated field and add simple field
         */
        $builder
            ->remove('plainPassword')
            ->add('plainPassword', 'password');
    }

    public function getParent()
    {
        return UserType::class;
    }

}

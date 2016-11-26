<?php

/**
 * @author Fondative <devteam@fondative.com>
 *
 * @ New FromType to update the user's account
 */

namespace Apr\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class EditFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', new UserEditType())
            ->add('contact', new ContactType());
    }
}

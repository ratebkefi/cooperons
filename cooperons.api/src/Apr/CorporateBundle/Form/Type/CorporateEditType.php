<?php
namespace Apr\CorporateBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CorporateEditType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->remove('country')
            ->remove('tvaIntracomm');

    }

    public function getParent()
    { 
        return CorporateType::class;
    }
}
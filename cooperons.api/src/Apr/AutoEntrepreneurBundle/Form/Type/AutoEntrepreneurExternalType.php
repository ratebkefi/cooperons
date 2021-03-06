<?php
namespace Apr\AutoEntrepreneurBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AutoEntrepreneurExternalType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('SIRET', TextType::class)
            ->add('externalLastName', TextType::class)
            ->add('externalFirstName', TextType::class)
            ->add('externalPassword', TextType::class)
            ->add('externalOldPassword', TextType::class)
            ->add('externalEmail', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Apr\AutoEntrepreneurBundle\Entity\AutoEntrepreneur',
        ));
    }
}

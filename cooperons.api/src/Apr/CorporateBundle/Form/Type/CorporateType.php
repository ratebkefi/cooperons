<?php
namespace Apr\CorporateBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CorporateType extends AbstractType
{
    private $countryUE;

    public function __construct($countryUE = null)
    {
        $this->countryUE = $countryUE;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('raisonSocial', TextType::class)
            ->add('tvaIntracomm', TextType::class)
            ->add('address', TextType::class)
            ->add('secondAddress', TextType::class)
            ->add('city', TextType::class)
            ->add('postalCode')
            ->add('country', EntityType::class, array(
                'class' => 'Apr\CorporateBundle\Entity\Country',
                'choice_label' => 'label'))
            ->add('phone', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Apr\CorporateBundle\Entity\Corporate',
            'error_mapping' => array(
                'tVAValid' => 'tvaIntracomm',
                //Idem ...
                //tVAValid => car application CamelCase SF ...

            ),
        ));
    }
}
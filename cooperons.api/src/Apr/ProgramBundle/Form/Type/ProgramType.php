<?php

namespace Apr\ProgramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Apr\CorporateBundle\Form\Type\UploadedFileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ProgramType extends AbstractType
{
    private $allCorporates;

    public function __construct($allCorporates) {
        $this->allCorporates = $allCorporates;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        $builder
            ->add('label', TextType::class)
            ->add('image', new UploadedFileType())
            ->add('collaborator', EntityType::class, array(
                'class' => 'Apr\CorporateBundle\Entity\Collaborator',
                'choice_label' => 'corporate.raisonSocial',
                'choices' => $this->allCorporates,
            ));
    }

    public function configureOptions(OptionsResolver $resolver) {
       $resolver->setDefaults(array(
            'data_class' => 'Apr\ProgramBundle\Entity\Program'
        ));
    }

}

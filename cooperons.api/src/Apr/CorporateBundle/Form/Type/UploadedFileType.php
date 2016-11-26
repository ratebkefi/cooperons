<?php
 
namespace Apr\CorporateBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class UploadedFileType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', 'file');
    }

    public function configureOptions(OptionsResolver $resolver) {
       $resolver->setDefaults(array(
            'data_class' => 'Apr\CorporateBundle\Entity\UploadedFile'
        ));
    }
}
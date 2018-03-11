<?php

namespace WebshopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProductType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('code')
            ->add('description')
            ->add('price')
            ->add('imagepath', FileType::class, array('label' => 'image'))
            ->add('imagepath' , FileType::class, [
                'data_class' => null,
                'empty_data' => $builder->getForm()->getData('Product')->getImagePath(),
                'label' => 'pdf file',
                'required' => false]);

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WebshopBundle\Entity\Product'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'webshopbundle_product';
    }



}

<?php

namespace App\Form;

use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints as Assert;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ...
            ->add('original_filename', FileType::class, ['label' => 'Photo (JPEG file)'])
            // ...
        ;
/*
        $builder->add(
            'image',
            FileType::class, [
                'required'    => true,
                'constraints' => [
                    new Assert\Image([
                        'minWidth'  => 200,
                        'maxWidth'  => 400,
                        'minHeight' => 200,
                        'maxHeight' => 400,
                    ])
                ]
            ]
        );*/

        
        
        
        
        
        
        
        
    /*
        $builder
            /*->add('original_filename')
            ->add('width')
            ->add('height')
            ->add('description')
            ->add('created_at')
            ->add('user_id')*-/
            ->add(
                'file',
                FileType::class
                //['label' => 'Brochure (PDF file)']
            )
        ;*/
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}

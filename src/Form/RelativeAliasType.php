<?php

namespace App\Form;

use App\Entity\RelativeAlias;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RelativeAliasType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('alias', null, [
                'label'    => 'but I call them',
                'empty_data' => '',
                'required' => false
            ])
            //->add('user')
            //->add('relativeUser')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RelativeAlias::class,
        ]);
    }
}

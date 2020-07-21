<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('addRating', ChoiceType::class,[
                'attr'=>['class'=>'js-input-addRating'],
                'placeholder' => 'No',
                'mapped' => false,
                'required'=> false,
                'data' => false,
                'choices' => ['Yes' => true],
//                'choice_attr' => function($choice, $key, $value) {
//                    // adds a class like attending_yes, attending_no, etc
//                    return ['class' => 'col-lg-3 col-md-4 col-6 attending_'.strtolower($key)];
//                },
            ])
            ->add('rating', NumberType::class,[
                'required'=> false,
                'html5'=>true,
                'attr' => [
                    'class' => 'js-addRating',
                    'min'=>0,
                    'max'=>5
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}

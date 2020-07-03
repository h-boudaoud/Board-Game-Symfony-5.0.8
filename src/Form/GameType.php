<?php

namespace App\Form;

use App\Entity\Game;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('imageUrl',UrlType::class,[

            ])
            ->add('yearPublished')
            ->add('minPlayers')
            ->add('maxPlayers')
            ->add('minPlaytime')
            ->add('maxPlaytime')
            ->add('minAge')
            ->add('price')
            ->add('msrp')
            ->add('discount')
            ->add('artistsList',null,[
                'help'=>'use comma as separator',
                'attr'=>['class'=>'js-add'],
                'required' => false,
            ])
            ->add('namesList',null,[
                'help'=>'use comma as separator',
                'attr'=>['class'=>'js-add'],
                'required' => false,
            ])
            ->add('publishersList',null,[
                'help'=>'use comma as separator',
                'attr'=>['class'=>'js-add'],
                'required' => false,
            ])
            ->add('rulesUrl')
            ->add('officialUrl')
            ->add('gameId')
            ->add('published', ChoiceType::class, [
                'placeholder' => 'Choose an option',
                'attr' => ['class' => "form-control"],
                'choices' => [
                    'Published' => true,
                    'Not published' => false,
                ],
            ])
            ->add('weightUnits', ChoiceType::class, [
                'placeholder' => 'Choose an option',
                'attr' => ['class' => "form-control"],
                'required'=>false,
                'choices' => Game::WEIGHT_UNITS,
                'choice_label' => function ($choice, $key, $value) {
                    if (true === $choice) {
                        return 'Definitely!';
                    }

                    return $value;

                    // or if you want to translate some key
                    //return 'form.choice.'.$key;
                },
            ])
            ->add('weightAmount', NumberType::class,[
                'attr' => [
                    'class' => "form-control js-weightUnits",
                    'min' => 0,
                    'step' => .1,
                ],
                'html5'=>true,
                'required'=>false,

            ])
            ->add('sizeUnits', ChoiceType::class, [
                'placeholder' => 'Choose an option',
                'attr' => ['class' => "form-control"],
                'required'=>false,
                'choices' => Game::SIZE_UNITS,
                'choice_label' => function ($choice, $key, $value) {
                    if (true === $choice) {
                        return 'Definitely!';
                    }

                    return $value;

                    // or if you want to translate some key
                    //return 'form.choice.'.$key;
                },
            ])
            ->add('sizeHeight', NumberType::class,[
                'attr' => [
                    'class' => "form-control js-sizeUnits",
                    'min' => 0,
                    'step' => .1,
                ],
                'html5'=>true,
                'required'=>false,

            ])
            ->add('sizeWidth', NumberType::class,[
                'attr' => ['class' => "form-control js-sizeUnits",
                    'min' => 0,
                    'step' => .1],
                'html5'=>true,
                'required'=>false,

            ])
            ->add('sizeDepth', NumberType::class,[
                'attr' => ['class' => "form-control js-sizeUnits",
                    'min' => 0,
                    'step' => .1],
                'html5'=>true,
                'required'=>false,

            ])
            ->add('primaryPublisher')
            ->add('description')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}

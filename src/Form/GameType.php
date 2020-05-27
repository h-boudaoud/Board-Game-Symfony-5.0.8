<?php

namespace App\Form;

use App\Entity\Game;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('yearPublished')
            ->add('minPlayers')
            ->add('maxPlayers')
            ->add('minPlaytime')
            ->add('maxPlaytime')
            ->add('minAge')
            ->add('description')
            ->add('price')
            ->add('msrp')
            ->add('discount')
            ->add('artists')
            ->add('names')
            ->add('publishers')
            ->add('rulesUrl')
            ->add('officialUrl')
            ->add('gameId')
            ->add('published')
            ->add('weightAmount')
            ->add('weightUnits')
            ->add('sizeHeight')
            ->add('sizeWidth')
            ->add('sizeDepth')
            ->add('sizeUnits')
            ->add('primaryPublisher')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}

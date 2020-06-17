<?php

namespace App\Form\User;

use App\Entity\Game;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class UserInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('firstName')
            ->add('lastName')
            ->add('birthday', DateType::class, [
                // renders it as a single text box
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control', 'type' => 'date'],
                'html5' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,

//           // Reduce Code Duplication with "inherit_data" 'inherit_data' => true,

        ]);
    }
}

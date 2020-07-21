<?php

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class EditeUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('userPassword', PasswordType::class,[
                'help'=>'The password is required to validate these modifications',
                'mapped'=>false
            ])
//            ->add('password', RegisterPasswordType::class,[
//                'label'=>false,
//                'attr'=>['class'=>'m-0 p-1'],
//                'inherit_data' => true,
//            ])
//            ->add('user_info', UserInfoType::class, [
//                //'label'=>false,
//                'attr'=>['class'=>'jumbotron m-0 p-1'],
//                'inherit_data' => true,
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

<?php

namespace App\Form\User;

use App\Entity\Customer;
use App\Entity\User;
use App\Form\CustomerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class UserProfileEditType extends AbstractType
{



    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $customer = $options["data"]->getCustomer()?$options["data"]->getCustomer():new Customer($options["data"]);
        $builder
            ->add('email')
            ->add('customer', CustomerType::class,[
                'label' => 'Customer infos',
                'inherit_data' => false,
                'attr'=>['class'=>'jumbotron m-0 p-1'],
                'data' => $customer,
            ])
            ->add('userPassword', PasswordType::class,[
                'label'=>'Your password to validate these modifications',
                'help'=>'The password is required to validate these modifications',
                'mapped'=>false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

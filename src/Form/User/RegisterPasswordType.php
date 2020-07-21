<?php


namespace App\Form\User;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', PasswordType::class,[
                'help'=>'The password must contain at least 8 characters, at least one lowercase letter, one capital letter, one numeric and one of the following characters: <,>, &, @, $, #,%, _, ~, ¤, £, !, §, *, (, [,),], /,., |, *, -, =',
            ])
            ->add('confirmPassword', PasswordType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

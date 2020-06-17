<?php

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class RoleUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user_infos', UserInfoType::class, [
                // 'label'=>false,
                'attr'=>['class'=>'jumbotron m-0 p-1'],
                'inherit_data' => true,
            ])
            ->add('roles', ChoiceType::class, [
                'multiple' =>true,
                'placeholder' => 'Choose one or more roles',
                'label'=>'Choose one or more roles',
                'attr' => ['class' => "form-control"],
                'required' => false,
                'choices' => User::ROLES,
                'choice_label' => function ($choice, $key, $value) {
                    if (true === $choice) {
                        return 'Definitely!';
                    }

                    $array = explode('_', $value);
                    array_shift($array);
                    //dd($array);
                    return ucfirst(implode('-', $array));

                    // or if you want to translate some key
                    //return 'form.choice.'.$key;
                },
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

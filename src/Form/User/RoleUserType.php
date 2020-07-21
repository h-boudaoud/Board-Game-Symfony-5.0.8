<?php

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;


class RoleUserType extends AbstractType
{
    private $roles;

    public function __construct(Security $security)
    {
        $roles = User::ROLES;
        if (!in_array('ROLE_SUPER_ADMIN', $security->getUser()->getRoles())) {
            array_shift($roles);
        }
        if (!in_array('ROLE_ADMIN', $security->getUser()->getRoles())) {
            $roles = array_unique(array_merge ($security->getUser()->getRoles(),['ROLE_USER']));
        }
        $this->roles = $roles;

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user_infos', UserInfoType::class, [
                // 'label'=>false,
                'attr' => ['class' => 'jumbotron m-0 p-1'],
                'inherit_data' => true,
            ])
            ->add('roles', ChoiceType::class, [
                'multiple' => true,
                'placeholder' => 'Choose one or more roles',
                'label' => 'Choose one or more roles',
                'attr' => ['class' => "form-control"],
                'required' => false,
                'choices' => $this->roles,
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

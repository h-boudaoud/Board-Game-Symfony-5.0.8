<?php

namespace App\Form;

use App\Entity\CustomerAddress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $customer = $options['data']->getCustomer();
        $builder
            ->add('deliveryAt')
            ->add('streetAddress')
            ->add('streetAddressLine2')
            ->add('city')
            ->add('state')
            ->add('postal')
            ->add('country')
                ->add('isMainAddress', ChoiceType::class, [
                    'label' => 'Is the main address?',
                    'mapped' => false,
                    'attr' => [
                        'id' => 'customer_newAddress',
                        'class' => 'row js-choice js-newAddress',
                    ],
                    'disabled'=>empty($customer->getMainAddress()),
                    'expanded' => true,
                    'placeholder' => false,
                    'required' => false,
                    'data' => !($customer && Count($customer->getAddresses())),
                    'choices' => ['No' => false, 'Yes' => true],
                    'choice_attr' => function ($choice, $key, $value) {
                        // adds a class like attending_yes, attending_no, etc
                        return ['class' => 'col-lg-3 col-md-4 col-6 attending_' . strtolower($key)];
                    },
                ])
            ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomerAddress::class,
        ]);
    }
}

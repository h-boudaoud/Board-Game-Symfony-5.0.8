<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\CustomerAddress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $customer =$options['data'];
        $address = (new CustomerAddress())->setCustomer($customer);
        //$address =$options['data']->getMainAddress();
        if (!$options['data']->getTitle()) {
            $builder
                ->add('Title', ChoiceType::class, [
                    'placeholder' => 'Choose an option',
                    'multiple' => false,
                    'attr' => ['class' => "form-control"],
                    'required' => true,
                    'choices' => Customer::TITLES,
                    'choice_label' => function ($choice, $key, $value) {
                        if (true === $choice) {
                            return 'Definitely!';
                        }
                        return ucfirst($value);

                    }
                ])
            ;
        }

        if(empty($customer->getAddresses)) {
            $builder
                ->add('newAddress', ChoiceType::class, [
                    'label' => 'Add new address',
                    'attr' => [
                        'id' => 'customer_newAddress',
                        'class' => 'form-control'
                    ],
                    'placeholder' => 'No',
                    'mapped' => false,
                    'required' => false,
                    'data' => false,
                    'choices' => ['Yes' => true],
//                'choice_attr' => function($choice, $key, $value) {
//                    // adds a class like attending_yes, attending_no, etc
//                    return ['class' => 'col-lg-3 col-md-4 col-6 attending_'.strtolower($key)];
//                },
                ]);
        }
        $builder
            ->add('addresses', CustomerAddressType::Class, [
                'label' => 'New address',
                'attr'=>['class'=>'bg-white m-2 p-1 js-newAddress'],
                'mapped' => false,
                'inherit_data' => false,
                'data' => $address,
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}

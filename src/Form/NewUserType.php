<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class NewUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'edit-family-form-field',
                    'placeholder' => 'Name'
                ],
            ])

            ->add('last_name', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'edit-family-form-field',
                    'placeholder' => 'Last Name'
                ],
            ])

            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'edit-family-form-field',
                    'placeholder' => 'Email'
                ],
            ])

            ->add('password', PasswordType::class, [
                'label' => false,
                'empty_data' => '',
                'attr' => [
                    'class' => 'edit-family-form-field',
                    'placeholder' => 'Password'
                ],
            ])
        
            ->add('Add_New_User', SubmitType::class, [
                'label' => 'Add User'
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
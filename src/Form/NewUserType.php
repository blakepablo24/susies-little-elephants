<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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

            ->add('Last_Name', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'edit-family-form-field',
                    'placeholder' => 'Last Name'
                ],
            ])

            ->add('email', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'edit-family-form-field',
                    'placeholder' => 'Email'
                ],
            ])

            ->add('password', PasswordType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'edit-family-form-field',
                    'placeholder' => 'Password'
                ],
            ])
        
            ->add('Add_New_User', SubmitType::class, [
                'attr' => [
                    'class' => 'submit-button',
                ],
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
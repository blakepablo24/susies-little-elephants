<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('email', TextType::class, [
            'label' => false,
            'attr' => [
                'readonly' => true,
                'class' => 'edit-family-form-field',
                'placeholder' => 'Email'
            ],
        ])

        ->add('password', PasswordType::class, [
            'label' => 'New Password: ',
            'attr' => [
                'class' => 'edit-family-form-field',
                'placeholder' => 'Password'
            ],
        ])

        ->add('Update_Password', SubmitType::class, [
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

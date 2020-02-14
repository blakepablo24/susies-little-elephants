<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class FrontContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'edit-family-form-field',
                'placeholder' => 'Your Name'
            ],
        ])
        ->add('email', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'edit-family-form-field',
                'placeholder' => 'Your Email'
            ],
        ])
        ->add('number', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'edit-family-form-field',
                'placeholder' => 'Your Number'
            ],
        ])
        ->add('subject', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'edit-family-form-field',
                'placeholder' => 'Subject'
            ],
        ])

        ->add('content', TextareaType::class, [
            'label' => false,
            'attr' => [
                'class' => 'edit-family-form-field',
                'placeholder' => 'PostDetails',
                'cols' => '30',
                'rows' => '10'
            ],
        ])

        ->add('Send', SubmitType::class, [
            'attr' => [
                'class' => 'edit-family-form-button'
            ],
        ])
    ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            
        ));
    }
}

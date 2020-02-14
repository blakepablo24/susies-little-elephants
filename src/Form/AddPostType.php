<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AddPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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

            ->add('image', FileType::class, [
                'label' => 'Enter an image (Jpeg or Png',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        // 'mimeTypes' => [
                        //     'jpg',
                        //     'png',
                        // ],
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ])
                ]
            ])

            ->add('Create_Post', SubmitType::class, [
                'attr' => [
                    'class' => 'edit-family-form-button'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
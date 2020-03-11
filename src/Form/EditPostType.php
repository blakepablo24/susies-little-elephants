<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class EditPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subject', TextType::class, [
                'label' => 'subject',
                'attr' => [
                    'class' => 'edit-family-form-field',
                    'placeholder' => 'Subject'
                ],
            ])

            ->add('content', CKEditorType::class, [
                'label' => false,
                'config' => array(
                    'uiColor' => '#ffffff',
                    'toolbar' => 'basic'
                ),
            ])

            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'The image must be either Jpeg or Png Format',
                        'maxSizeMessage' => 'The Image cannot be bigger than 2 MB'
                    ])
                ]
            ])

            ->add('Update', SubmitType::class, [
                'attr' => [
                    'class' => 'submit-button'
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

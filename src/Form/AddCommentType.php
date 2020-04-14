<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AddCommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('content', CKEditorType::class, [
            'label' => false,
            'config' => array(
                'uiColor' => '#ffffff',
                'toolbar' => 'basic',
                'width' => '100%',
                'height' => '100'
            ),
        ])

            ->add('Update', SubmitType::class, [
                'label' => 'Add Comment',
                'attr' => [
                    'class' => 'submit-button',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}

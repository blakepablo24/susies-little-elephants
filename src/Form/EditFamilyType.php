<?php

namespace App\Form;

use App\Entity\Family;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EditFamilyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', TextType::class, [
                'label' => 'Family',
                'attr' => [
                    'class' => 'edit-family-form-field',
                ],
            ])

            ->add('mum', TextType::class, [
                'label' => 'Mother',
                'attr' => [
                    'class' => 'edit-family-form-field',
                ],
            ])
                
            ->add('dad', TextType::class, [
                'label' => 'Father',
                'attr' => [
                    'class' => 'edit-family-form-field',
                ],
            ])

            ->add('guardian', TextType::class, [
                'label' => 'Guardian',
                'attr' => [
                    'class' => 'edit-family-form-field',
                ],
            ])

            ->add('guardian', TextType::class, [
                'label' => 'Guardian',
                'attr' => [
                    'class' => 'edit-family-form-field',
                ],
            ])

            ->add('Update', SubmitType::class, [
                'attr' => [
                    'class' => 'submit-button',
                ],
            ])
        ;

        $builder->add('children', CollectionType::class, [
            'entry_type' => EditChildType::class,
            'entry_options' => [
                'attr' => [
                    'class' => 'edit-family-form-field',
                ],
            ],
        ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Family::class,
        ]);
    }
}

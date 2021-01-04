<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Username...',
                    'class' => 'form-control'
                ]
            ])
            ->add('nom', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Nom...',
                    'class' => 'form-control'
                ]
            ])
            ->add('prenom', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Prénom...',
                    'class' => 'form-control'
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'ROLE_SUPER_ADMIN' => 'ROLE_SUPER_ADMIN',
                    'ROLE_USER' => 'ROLE_USER'
                ],
                'expanded' => true,
                'multiple' => true,
                'attr' => [
                    'class' => 'form-group'
                ]
            ])
            ->add('enabled', ChoiceType::class, [
                'choices' => [
                    'OUI' => true, 'NON' => false
                ],
                'empty_data' => false,
                'expanded' => true,
                'multiple' => false,
                'attr' => [
                    'class' => 'form-group'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

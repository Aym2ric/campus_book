<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserRegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Email...',
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
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'options' => [
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Mot de passe...'
                    ]
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

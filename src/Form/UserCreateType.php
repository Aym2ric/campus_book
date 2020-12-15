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

class UserCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                "required" => true,
                'attr' => ['placeholder' => 'Username...', 'class' => 'form-control']
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class, 'required' => true,
                'options' => ['attr' => ['class' => 'form-control', 'placeholder' => 'Mot de passe...']]
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => ['ROLE_SUPER_ADMIN' => 'ROLE_SUPER_ADMIN', 'ROLE_USER' => 'ROLE_USER'],
                'expanded' => true, 'multiple' => true,
                'attr' => ['class' => 'form-group']
            ])
            ->add('enabled', ChoiceType::class, [
                'choices' => ['Oui' => true, 'Non' => false],
                "data" => true,
                'expanded' => true, 'multiple' => false,
                'attr' => ['class' => 'form-group']
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

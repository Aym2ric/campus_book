<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, ['attr' => ['placeholder' => 'Username...', 'class' => 'form-control']])
            ->add('password', TextType::class, ['attr' => ['placeholder' => 'Mot de passe...', 'class' => 'form-control']])
            ->add('roles', ChoiceType::class, ['choices' => ['ROLE_SUPER_ADMIN' => 'ROLE_SUPER_ADMIN', 'ROLE_USER' => 'ROLE_USER'], 'expanded' => true, 'multiple' => true, 'attr' => ['class' => '']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

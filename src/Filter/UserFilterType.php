<?php

namespace App\Filter;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\ChoiceFilterType;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\TextFilterType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextFilterType::class, [
                'label' => 'Email',
                'label_attr' => [
                    'class' => 'text-grey'
                ],
                "attr" => [
                    "placeholder" => "Username..."
                ],
                'required' => false
            ])
            ->add('nom', TextFilterType::class, [
                'label' => 'Nom',
                'label_attr' => [
                    'class' => 'text-grey'
                ],
                "attr" => [
                    "placeholder" => "Nom..."
                ],
                'required' => false
            ])
            ->add('prenom', TextFilterType::class, [
                'label' => 'Prénom',
                'label_attr' => [
                    'class' => 'text-grey'
                ],
                "attr" => [
                    "placeholder" => "Prénom..."
                ],
                'required' => false
            ])
            ->add('roles', ChoiceFilterType::class, [
                'label' => 'Rôles',
                'label_attr' => [
                    'class' => 'text-grey'
                ],
                'choices' => [
                    'ROLE_SUPER_ADMIN' => 'ROLE_SUPER_ADMIN',
                    'ROLE_USER' => 'ROLE_USER'
                ],
                'required' => false
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'Actif ?',
                'label_attr' => [
                    'class' => 'text-grey custom-control-label'
                ],
                'attr' => [
                    'class' => 'custom-control-input',
                ],
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}

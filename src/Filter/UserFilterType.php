<?php

namespace App\Filter;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\ChoiceFilterType;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\TextFilterType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextFilterType::class, [
                "attr" => ["placeholder" => "Username..."]
            ])
            ->add('roles', ChoiceFilterType::class, [
                'choices' => ['ROLE_SUPER_ADMIN' => 'ROLE_SUPER_ADMIN', 'ROLE_USER' => 'ROLE_USER'],
                "expanded" => true, "multiple" => true, "empty_data" => null,
                "placeholder" => "-- Rôles --"
            ])
            ->add('enabled', ChoiceFilterType::class, [
                'choices' => ['Oui' => true, 'Non' => false],
                "data" => true, "required" => true,
                'expanded' => true, 'multiple' => false,
                'attr' => ['class' => 'form-group']
            ]);
    }

    public function getBlockPrefix()
    {
        return 'user_filter';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}
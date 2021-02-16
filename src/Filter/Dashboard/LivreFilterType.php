<?php

namespace App\Filter\Dashboard;

use App\Entity\Etat\LivreEtat;
use App\Entity\Theme;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\ChoiceFilterType;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\TextFilterType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LivreFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextFilterType::class, [
                'label' => false,
                'label_attr' => [
                    'class' => ''
                ],
                "attr" => [
                    "placeholder" => "Titre...",
                    'class' => 'borders-hidden'
                ],
                'required' => false
            ])
            ->add('theme', EntityType::class, [
                'class' => Theme::class,
                'choice_label' => 'nom',
                'label' => false,
                'label_attr' => [
                    'class' => ''
                ],
                "placeholder" => "Thème...",
                "attr" => [
                    'class' => 'borders-hidden h-100'
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

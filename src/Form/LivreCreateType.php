<?php

namespace App\Form;

use App\Entity\Etat\LivreEtat;
use App\Entity\Livre;
use App\Entity\Theme;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LivreCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Nom...',
                    'class' => 'form-control'
                ]
            ])
            ->add('auteur', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Auteur...',
                    'class' => 'form-control'
                ]
            ])
            ->add('dateSortie', DateType::class, [
                'years' => range(date('Y')-200, date('Y')+5)
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Description...',
                    'class' => 'form-control'
                ]
            ])
            ->add('etat', ChoiceType::class, [
                'choices' => LivreEtat::getEtatsForSelect(),
            ])
            ->add('nbJoursPret', NumberType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Nombre de jours de prêt...',
                    'class' => 'form-control'
                ]
            ])
            ->add('theme', EntityType::class, [
                'label' => 'Thème',
                'label_attr' => [
                    'class' => 'font-weight-bold'
                ],
                'class' => Theme::class,
                'attr' => [
                    'class' => 'form-select',
                    'data-placeholder' => 'Thème...'
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')->orderBy('t.nom', 'ASC');;
                },
                'choice_label' => 'nom',
                'placeholder' => ''
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Livre::class,
        ]);
    }
}

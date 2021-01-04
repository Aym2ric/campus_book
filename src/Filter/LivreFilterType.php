<?php

namespace App\Filter;

use App\Entity\Etat\LivreEtat;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\ChoiceFilterType;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\TextFilterType;
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
                'label' => 'Nom',
                'label_attr' => [
                    'class' => 'text-grey'
                ],
                "attr" => [
                    "placeholder" => "Nom..."
                ],
                'required' => false
            ])
            ->add('etat', ChoiceType::class, [
                'choices' => LivreEtat::getEtatsForSelect(),
            ])
            ->add('bloquerProchaineReservation', CheckboxType::class, [
                'label' => 'Réservation bloqué ?',
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

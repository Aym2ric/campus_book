<?php

namespace App\Form;

use App\Entity\Etat\LivreEtat;
use App\Entity\Livre;
use App\Entity\Theme;
use App\Entity\User;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\FormInterface;

class LivrePreterType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ThemeRepository
     */
    private $themeRepository;

    /**
     * LivreCreateType constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, ThemeRepository $themeRepository)
    {
        $this->entityManager = $entityManager;
        $this->themeRepository = $themeRepository;
    }

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
            ->add('anneeSortie', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Année...',
                    'class' => 'form-control'
                ]
            ])
            ->add('theme', EntityType::class, [
                'required' => true,
                'class' => Theme::class,
                'choice_label' => 'nom',
                'attr' => [
                    'placeholder' => 'Thème...',
                    'class' => 'form-control'
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Description...',
                    'class' => 'form-control'
                ]
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Image',
                'label_attr' => [
                    'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Image...'
                ],
                'required' => false,
                'allow_delete' => false,
                'download_label' => false,
                'image_uri' => false
            ])
            ->add('urlImage', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Url Image...',
                    'class' => 'form-control'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Livre::class,
        ]);
    }
}

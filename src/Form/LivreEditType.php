<?php

namespace App\Form;

use App\Entity\Etat\LivreEtat;
use App\Entity\Livre;
use App\Entity\Theme;
use App\Entity\Type;
use App\Repository\ThemeRepository;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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

class LivreEditType extends AbstractType
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
     * @var TypeRepository
     */
    private $typeRepository;

    /**
     * LivreCreateType constructor.
     * @param EntityManagerInterface $entityManager
     * @param TypeRepository $typeRepository
     */
    public function __construct(EntityManagerInterface $entityManager, TypeRepository $typeRepository, ThemeRepository $themeRepository)
    {
        $this->entityManager = $entityManager;
        $this->typeRepository = $typeRepository;
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
            ->add('dateSortie', DateType::class, [
                'years' => range(date('Y') - 200, date('Y') + 5)
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
            ->add('bloquerProchaineReservation', ChoiceType::class, [
                'choices' => [
                    'Oui' => 1,
                    'Non' => 0
                ],
                'attr' => [
                    'class' => 'form-group'
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
                'allow_delete' => false,
                'download_label' => false,
                'image_uri' => false
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));

    }

    protected function addElements(FormInterface $form, Type $type = null)
    {
        $form
            ->add('type', EntityType::class, [
                'label' => 'Type',
                'label_attr' => [
                    'class' => 'font-weight-bold'
                ],
                'class' => Type::class,
                'attr' => [
                    'class' => 'form-select',
                    'data-placeholder' => 'Type...'
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')->orderBy('t.nom', 'ASC');;
                },
                'choice_label' => 'nom',
                'placeholder' => ''
            ]);

        $themes = [];

        if ($type) {
            $qb = $this->themeRepository->createQueryBuilder("t")
                ->where("t.type = :type_id")
                ->setParameter("type_id", $type->getId());

            $themes = $qb->getQuery()->getResult();
        }

        $form->add('theme', EntityType::class, [
            'label' => 'Thème',
            'label_attr' => [
                'class' => 'font-weight-bold'
            ],
            'class' => Theme::class,
            'attr' => [
                'class' => 'form-select',
                'data-placeholder' => 'Thème...'
            ],
            'choices' => $themes,
            'choice_label' => 'nom',
            'placeholder' => ''
        ]);
    }

    function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $qb = $this->typeRepository->createQueryBuilder("t")
            ->where("t.id = :type_id")
            ->setParameter("type_id", $data['type']);

        $type = $qb->getQuery()->getOneOrNullResult();

        $this->addElements($form, $type);
    }

    function onPreSetData(FormEvent $event)
    {
        $livre = $event->getData();
        $form = $event->getForm();

        // When you create a new person, the City is always empty
        $type = $livre->getType() ? $livre->getType() : null;

        $this->addElements($form, $type);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Livre::class,
        ]);
    }
}

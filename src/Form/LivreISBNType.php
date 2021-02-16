<?php

namespace App\Form;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LivreISBNType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isbn', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Ex: 9788804621430',
                    'class' => 'form-control'
                ]
            ]);
    }

    // public function configureOptions(OptionsResolver $resolver)
    // {
    //     $resolver->setDefaults([
    //         'nom' => String::class,
    //     ]);
    // }
}

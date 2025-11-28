<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire de gestion d'un produit dans le back-office.
 */
class ProductFormType extends AbstractType
{
    /**
     * Construction du formulaire avec les champs nécessaires.
     * 
     * @param FormBuilderInterface $builder Le constructeur de formulaire.
     * @param array $options Les options du formulaire.
     * 
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix du produit',
                'scale' => 2,
                'html5' => true,
            ])
            ->add('stockXs', IntegerType::class, [
                'label' => 'Stock disponible en XS',
            ])
            ->add('stockS', IntegerType::class, [
                'label' => 'Stock disponible en S',
            ])
            ->add('stockM', IntegerType::class, [
                'label' => 'Stock disponible en M',
            ])
            ->add('stockL', IntegerType::class, [
                'label' => 'Stock disponible en L',
            ])
            ->add('stockXl', IntegerType::class, [
                'label' => 'Stock disponible en XL',
            ]);
    }

    /**
     * Configuration des options du formulaire.
     * 
     * @param OptionsResolver $resolver Le résolveur d'options.
     * 
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
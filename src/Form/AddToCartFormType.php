<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * Formulaire d'ajout au panier.
 *
 * Permet à l'utilisateur de choisir une taille avant
 * d'ajouter un sweat au panier.
 */
class AddToCartFormType extends AbstractType
{
    /**
     * Construction du formulaire AddToCartForm
     * 
     * @param FormBuilderInterface $builder
     * @param array $option
     * 
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('size', ChoiceType::class, [
                'label' => 'Taille',
                'choices' => [
                    'XS' => 'XS',
                    'S' => 'S',
                    'M' => 'M',
                    'L' => 'L',
                    'XL' => 'XL',    
                ],
                'expanded' => false,
                'multiple' => false,
            ])
            -> add('submit', SubmitType::class, [
                'label' => 'Ajouter au panier',
                'attr' => [
                    'class' => 'btn add-to-cart-btn',
                ],
            ])
        ;
    }

}

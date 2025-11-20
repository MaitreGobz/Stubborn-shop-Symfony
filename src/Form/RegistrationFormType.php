<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Formulaire d'inscription d'un nouvel utilisateur.
 *
 * Ce formulaire permet de collecter les informations nécessaires
 * à la création d'un compte client pour la boutique Stubborn :
 * - nom d'utilisateur
 * - adresse e-mail
 * - adresse de livraison
 * - mot de passe (saisi deux fois)
 */
class RegistrationFormType extends AbstractType
{
    /**
     * Configure les champs du formulaire d'inscription.
     *
     * @param FormBuilderInterface $builder Constructeur de formulaire.
     * @param array<string,mixed>  $options Options du formulaire.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            // Name displayed on the website (customer account)
            ->add('name', TextType::class, [
                'label' => 'Nom utilisateur',
                'constraints' => [
                    new NotBlank(message: 'Veuillez saisir un nom.'),
                    new Length(min: 3, max: 180, minMessage: 'Le nom doit contenir au moins {{ limit }} caractères.'),
                ],
            ])

            // Email address used for login and confirmation
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'constraints' => [
                    new NotBlank(message: 'Veuillez saisir une adresse mail valide.'),
                ],
            ])
            
            // Postal address used for delivery
            ->add('deliveryAddress', TextareaType::class, [
                'label' => 'Adresse de livraison',
                'constraints' => [
                    new NotBlank(message: 'Veuillez saisir votre adresse de livraison.'),
                    new Length(min: 10, max: 1000, minMessage: 'L\'adresse doit contenir au moins {{ limit }} caractères.'),
                ],
            ])
            
            // Password entered twice
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options'  => [
                    'label' => 'Mot de passe',
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                ],
                'invalid_message' => 'Les mots de passe doivent être identiques.',
                'constraints' => [
                    new NotBlank(message: 'Veuillez saisir un mot de passe'),
                    new Length(min: 6, max: 4096, minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.'),
                ],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

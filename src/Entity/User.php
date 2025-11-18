<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Représente un utilisateur du site e-commerce Stubborn.
 *
 * L'utilisateur peut être un client ou un administrateur.
 * Cette entité gère :
 * - l'authentification via email/mot de passe
 * - les rôles attribués (ROLE_USER, ROLE_ADMIN)
 * - la vérification du compte après inscription (email de confirmation)
 *
 * Elle est stockée dans la base de données via Doctrine ORM.
 */

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * Identifiant unique auto-généré de l'utilisateur.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Adresse email servant d'identifiant de connexion.
     */
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * Liste des rôles attribués à l'utilisateur.
     *
     * Exemple : ["ROLE_USER"], ["ROLE_ADMIN"]
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * Mot de passe hashé.
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * Nom de l'utilisateur
     */
    #[ORM\Column(length: 225)]
    private ?string $name = null;

    /**
     * Adresse de livraison.
     */
    #[ORM\Column(type: Types::TEXT)]
    private ?string $deliveryAddress = null;

    /**
     * Statut indiquant si l'utilisateur a confirmé son inscription par email.
     *
     * false = compte non activé
     * true  = compte activé
     */
    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Identifiant visuel représentant cet utilisateur.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDeliveryAdress(): ?string
    {
        return $this->deliveryAdress;
    }

    public function setDeliveryAdress(string $deliveryAdress): static
    {
        $this->deliveryAdress = $deliveryAdress;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * Evite que la session ne contienne le mot de passe hashé
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, conservée pour compatibilité Symfony 8
    }
}

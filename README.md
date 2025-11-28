# 🧵 Stubborn — Boutique e-commerce Symfony

_Boutique en ligne de sweat-shirts réalisée dans le cadre du devoir « Réaliser un site e-commerce avec Symfony ». 📚_

## 📌 Description du projet

Stubborn est une application e-commerce développée avec **Symfony** permettant à la marque fictive **Stubborn** de vendre sa collection de sweat-shirts.  
Ce projet respecte l’intégralité des exigences du sujet du devoir, notamment :

-   Framework **Symfony**
-   Base de données **MySQL**
-   Authentification complète (Security)
-   Rôles utilisateurs (`ROLE_USER`, `ROLE_ADMIN`)
-   Panier fonctionnel stocké en session
-   Intégration Stripe Checkout (paiement test)
-   Back-office administrateur complet
-   Tests unitaires
-   Vues conformes aux **wireframes** du sujet
-   Documentation incluse au format PDF

---

## 🚀 Fonctionnalités

### 👤 Authentification & gestion utilisateurs

-   Inscription avec **email de confirmation** via **MailTrap**
-   Connexion / déconnexion
-   Page `/login` + `/register`
-   Accès restreint à certaines pages (panier, boutique…)
-   Deux rôles :
    -   `ROLE_USER`
    -   `ROLE_ADMIN`

---

### 💳 Paiement Stripe (mode test)

-   Intégration Stripe Checkout
-   Clés API configurées dans `.env.local`
-   Pages :
    -   `/checkout` → redirection Stripe
    -   `/success` → confirmation + panier vidé

---

### 🛠️ Back-office (`/admin`)

Accessible uniquement aux administrateurs.

Fonctionnalités :

-   Ajouter un sweat-shirt
-   Modifier prix / image / mise en avant
-   Modifier stock pour chaque taille
-   Supprimer un produit

---

## 🧱 Architecture

### Entités principales

-   `User`
-   `Product`

### Services

-   `CartService`
-   `StripeService`

### Dossiers clés

-   /src
-   /Controller
-   /Entity
-   /Form
-   /Repository
-   /Security
-   /Service
-   /templates
-   /public

## 🧪 Tests

Tests réalisés :

-   Tests untitaires pour les différentes fonctionnalités

Exécuter les tests :

```bash
php bin/phpunit
```

## 🏗️ Installation du projet :

1. Cloner le projet:

```bash
git clone https://github.com/MaitreGobz/Stubborn-shop-Symfony
cd stubborn_shop
```

2. Installer les dépendances

```bash
composer install
npm install
npm run build
```

3. Configurer `.env.local` :

```bash
DATABASE_URL="mysql://root:@127.0.0.1:3306/stubborn_shop?serverVersion=8.0"
MAILER_DSN=smtp://...
STRIPE_PUBLIC_KEY=...
STRIPE_SECRET_KEY=...
```

4. Préparer la base de données :

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

5. Lancer le serveur :

```bash
symfony serve
```

## 💳 Essayer un paiement

-   Numéro : 4242 4242 4242 4242
-   Date : 12/34
-   CVC : 123

## 🧰 Accès administrateur

Créer un mot de passe hashé :

```bash
php bin/console security:hash-password
```

Insérer l'utilisateur dans la base avec `ROLE_ADMIN`

Back-office :
👉[http://127.0.0.1:8000/admin/](http://127.0.0.1:8000/admin/)

## 📚 Documentation

Une documentation complète est fournie au format PDF dans le dépôt:

`docs\Stubborn site eCommerce - Documentation technique.pdf`

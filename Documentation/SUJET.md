# Projet : Création d’un Backoffice pour une Entreprise

## Contexte
Une petite entreprise de gestion de clients et de produits souhaite moderniser ses outils internes en créant un backoffice web pour ses employés. Ce backoffice permettra aux utilisateurs ayant des rôles spécifiques (ex. : Admin, Employé) de gérer les données de manière efficace et sécurisée.
Vous avez le libre choix de choisir quel type de produits votre entreprise gère.
Le projet a pour objectif de leur fournir une interface utilisateur simple mais robuste, leur permettant de :
- Gérer les utilisateurs : Ajouter, modifier, et supprimer des utilisateurs.
- Gérer les produits : Importer une liste de produits depuis un fichier CSV, consulter et exporter cette liste.
- Gérer les clients : Lister, filtrer et modifier les clients.
Visuellement, le projet devra comporter un menu de navigation à gauche avec les différentes sections (Utilisateurs, Produits, Clients). Le backoffice devra être sécurisé avec une connexion et les utilisateurs devront être authentifiés pour accéder aux différentes fonctionnalités.
Attention : Toute utilisation de bundle comme EasyAdminBundle ou autre est interdite. Vous devez coder vous-même les fonctionnalités demandées.

## Étape 1 : Mise en place du projet
1. Créez un nouveau projet Symfony en clonant le [boilerplate](https://github.com/chrisdemon8/symfony_base).
2. Ajoutez un .env.local à la racine de votre projet afin d'y ajouter votre chaîne de connexion à la base de données.
3. Ajoutez Tailwind CSS ou Bootstrap pour styliser votre projet.

Exemple avec Tailwind CSS :
[TailwindBundle](https://symfony.com/bundles/TailwindBundle/current/index.html)

```bash
composer require symfonycasts/tailwind-bundle
php bin/console tailwind:init
```

Ensuite pour compiler :

```bash
php bin/console tailwind:build
```

### Entités
- User : id, email, firstname, lastname, roles, password.
(Vous pouvez utiliser le maker pour cela : [Security](https://symfony.com/doc/current/security.html))
- Product : id, name, description, price.

### Rôles
- ROLE_USER : Utilisateur standard.
- ROLE_ADMIN : Administrateur.
- ROLE_MANAGER : Gestionnaire.

### Fixtures
1. Créez des fixtures pour ajouter des utilisateurs avec les rôles ROLE_ADMIN, ROLE_USER et ROLE_MANAGER.

## Connexion et Authentification
1. Créez un formulaire de connexion et inscription (simple) pour permettre aux utilisateurs de se connecter au backoffice.
À la place du formulaire d'inscription vous pouvez ajouter des fixtures pour ajouter des utilisateurs.

## Étape 2 : Gestion des utilisateurs
1. Ajout d’un onglet "Utilisateurs" (visible uniquement par un administrateur).
2. Implémentation d’un Voter pour restreindre l’accès selon le rôle.
3. Création d’une vue :
- Lister les utilisateurs (email, rôle, nom, prénom).
- Ajouter, modifier (tout sauf mot de passe) et supprimer un utilisateur (administrateur uniquement).

## Étape 3 : Gestion des produits
1. Ajout d’un onglet "Produits" (visible par tous les utilisateurs).
2. Implémentation d’un Voter pour gérer l’accès.
3. Création d’une vue :
- Ajouter, modifier et supprimer un produit (administrateur uniquement, masquez les boutons si le rôle n'est pas le bon).
- L'ajout et la modification doivent intégrer un formulaire multi‑étapes (Symfony 7.4) avec le comportement suivant :
Lorsqu’un administrateur clique sur « Ajouter un produit », il est redirigé vers un formulaire multi-étapes.
Chaque étape correspond à un écran distinct, comprenant :
- un titre clair (ex. : Type de produit, Détails, Logistique…),
- un formulaire partiel,
- des boutons Suivant / Précédent,
- une indication visuelle de progression avec des étapes numérotées.
Le nombre d’étapes et leur contenu varient dynamiquement selon les choix effectués dans les premières étapes.

#### Comportement visuel attendu
- Une seule étape est visible à la fois.
- Les données saisies sont conservées entre les étapes.
- Les étapes non pertinentes ne sont pas affichées.
- La dernière étape affiche un récapitulatif avant validation.

#### Exemples de logique dynamique
- Produit physique : une étape « Logistique » apparaît (poids, dimensions, stock, etc.).
- Produit numérique : l’étape « Logistique » est ignorée, une étape « Licence / accès » apparaît.
- Prix supérieur à un seuil défini : une étape intermédiaire de confirmation est ajoutée, validation explicite via une checkbox.

#### Architecture recommandée
- Form/Product/Step/ : un formulaire par étape
    - ProductTypeStepType (type de produit)
    - ProductDetailsStepType (name, description, price)
    - ProductLogisticsStepType (produit physique)
    - ProductLicenseStepType (produit numérique)
- Form/Product/ProductFlowType : configuration globale du formulaire multi-étapes

Le composant MultiStepForm de Symfony 7.4 est utilisé pour gérer la navigation et l’enchaînement des étapes.
1. Ajout d’une requête personnalisée pour trier les produits par prix décroissant. Utilisez cette requête pour afficher les produits triés dans la vue.
2. Ajout d’une fonctionnalité d’exportation CSV au clic sur un bouton avec les colonnes name, description, price. Cette fonction devra être faite dans un Service.
3. Création d’une commande Symfony pour importer un fichier CSV contenant des produits.
Le fichier CSV comportera un en-tête avec les colonnes suivantes : name, description, price.
Les id seront générés automatiquement lors de l'importation.
Le fichier à importer pourra être placé dans le dossier public du projet.

Utilisez la doc [officielle](https://symfony.com/doc/current/console.html#creating-a-command) pour vous aider.

## Étape 4 : Gestion des clients
1. Création de l'entité Client avec les champs :
- id, firstname, lastname, email, phoneNumber, address, createdAt.

1. Ajout d’un onglet "Clients" (visible par les gestionnaires et administrateurs).
2. Implémentation d’un Voter pour restreindre l’accès.
3. Création d’une vue :
- Lister les clients.
- Ajouter et modifier un client (administrateur et gestionnaire uniquement).
1. Ajout de données de test avec des fixtures.
2. Ajout d’une commande pour ajouter des clients en ligne de commande. La commande demandera le nom, prénom, l'email, le numéro de téléphone et l'adresse.
3. Ajout de validations sur les champs :
- Vérification du format de l’email.
- Vérification des champs firstname et lastname (non vides et sans caractères spéciaux).
- Vérification de l’unicité de l’email client. L'adresse mail ne doit pas être rattaché à un autre client.

## Étape 5: Tests
1. Écriture de tests unitaires :
- Test d’un service du projet.
- Test de la création d'un utilisateur, produit ou client. (avec un mock ou stub)

## Livrables Finaux :
1. Code source complet hébergé sur GitHub. (ajouter un fichier .gitignore à la racine de votre projet pour ne pas inclure les vendors ou autres fichiers inutiles)
2. Un fichier README.md décrivant par exemple :
- L'installation du projet.
- Les fonctionnalités implémentées.
- Comment exécuter les tests.
- Une vidéo montrant les différentes fonctionnalités serait la bienvenue.

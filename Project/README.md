# Symfony Backoffice Project

Ce projet est un backoffice de gestion pour une entreprise, réalisé avec Symfony 7.4 et Tailwind CSS.

## Fonctionnalités

### Authentification & Utilisateurs
- **Connexion/Déconnexion** sécurisée.
- **Gestion des utilisateurs** (CRUD) accessible uniquement aux Administrateurs.
- Rôles : `ROLE_ADMIN`, `ROLE_MANAGER`, `ROLE_USER`.

### Gestion des Produits
- **Calcul de prix** et affichage trié par prix décroissant.
- **Formulaire Multi-étapes** pour la création/édition de produits (logique dynamique selon le type physique/numérique).
- **Export CSV** des produits.
- **Import CSV** via commande Symfony.
- Sécurisé par des Voters (Édition/Suppression pour Admin seulement).

### Gestion des Clients
- **CRUD Clients** accessible aux Managers et Administrateurs.
- **Validations** : Email unique, format téléphone, noms sans caractères spéciaux.
- **Commande CLI** pour créer un client rapidement.

## Installation

1. **Cloner le projet**
   ```bash
   git clone <url_du_repo>
   cd symfony_project
   ```

2. **Installer les dépendances**
   ```bash
   composer install
   ```
   *Note : Le bundle `symfonycasts/tailwind-bundle` installera le binaire Tailwind automatiquement.*

3. **Configurer la base de données**
   Modifiez le fichier `.env.local` avec vos accès à la base de données.
   ```env
   DATABASE_URL="mysql://user:password@127.0.0.1:3306/db_name?serverVersion=8.0"
   ```

4. **Créer la base et les tables**
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:schema:update --force
   # Ou via migrations si fonctionnel : php bin/console doctrine:migrations:migrate
   ```

5. **Charger les données de test (Fixtures)**
   ```bash
   php bin/console doctrine:fixtures:load -n
   ```
   Comptes créés :
   - Admin : `admin@test.com` / `password`
   - Manager : `manager@test.com` / `password`
   - User : `user@test.com` / `password`

6. **Compiler les assets (Tailwind)**
   ```bash
   php bin/console tailwind:build
   ```

## Utilisation des Commandes

**Importer des produits depuis un CSV :**
```bash
php bin/console app:import-products public/products.csv
```

**Créer un client en ligne de commande :**
```bash
php bin/console app:create-client
```

## Tests
Pour lancer les tests (si configurés) :
```bash
php bin/console test
```

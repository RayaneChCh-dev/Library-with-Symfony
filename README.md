# Projet Bibliothèque - API REST Symfony

## Description

API publique REST développée avec Symfony 6.4 pour gérer une bibliothèque avec des livres, des auteurs, des catégories et un système d'emprunts.

## Contexte du projet

Cette API permet de :
- Gérer un catalogue de livres avec leurs auteurs et catégories
- Gérer les emprunts de livres par les utilisateurs
- Appliquer des règles métier (limite d'emprunts, disponibilité)
- Consulter l'historique des emprunts

## Prérequis

- PHP 6.4
- Composer
- MySQL
- Symfony CLI

## Installation

### 1. Cloner le projet

```bash
git clone [url-du-repo]
cd bibliotheque
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Configuration de la base de données

Dupliquer le fichier `.env` en `.env.local` et configurer la connexion à la base de données :

```bash
DATABASE_URL="mysql://root:password@127.0.0.1:3306/bibliotheque?serverVersion=8.0"
```

### 4. Créer la base de données

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```


## Routes API

### Livres (CRUD)

- `GET /api/v1/books/rent` - Liste tous les livres emprunté d'un auteur
- `GET /api/v1/books/search` - Recherche les livres empruntés

### Emprunts

- `POST /api/v1/books/rent` - Emprunter un livre
- `POST /api/v1/books/rent/restore` - Retourner un livre

## Format des données

Toutes les requêtes et réponses utilisent le format JSON.


## Codes HTTP utilisés

- `200 OK` - Requête réussie
- `201 Created` - Ressource créée avec succès
- `404 Not Found` - Ressource non trouvée
- `422 Unprocessable Entity` - Violation de règle métier
- `500 Internal Server Error` - Erreur serveur

## Développement

### Lancer le serveur de développement

```bash
symfony server:start
```

ou

```bash
php -S localhost:8000 -t public
```


## Logs et débogage

Les logs de l'application se trouvent dans `var/log/request.log`.

En cas d'erreur, l'API ne renvoie jamais d'informations sensibles. Consultez les logs pour le débogage détaillé.

## Sécurité

- Validation des types des paramètres d'URL
- Validation des données d'entrée avec le composant Validator de Symfony
- Contraintes HTTP sur les routes (GET, POST, PUT, DELETE)
- Pas d'exposition d'informations sensibles dans les réponses

## Technologies utilisées

- Symfony 6.4
- Doctrine ORM
- PHP 8.1+
- MySQL/PostgreSQL

## Auteur

- Rayane Achouchi
- Mathéo Vovard
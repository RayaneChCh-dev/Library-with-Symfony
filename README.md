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

- PHP 8.1 ou supérieur
- Composer
- MySQL 8.0 ou PostgreSQL 15
- Symfony CLI (optionnel mais recommandé)

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

### 5. (Optionnel) Charger des données de test

```bash
php bin/console doctrine:fixtures:load
```

## Structure de la base de données

### Entité Livre
- id (int, auto)
- titre (string, 255)
- datePublication (date, nullable)
- disponible (boolean)
- auteur (relation ManyToOne vers Auteur)
- categorie (relation ManyToOne vers Categorie)

### Entité Auteur
- id (int, auto)
- nom (string, 255)
- prenom (string, 255)
- biographie (text, nullable)
- dateNaissance (date, nullable)

### Entité Catégorie
- id (int, auto)
- nom (string, 255)
- description (text, nullable)

### Entité Utilisateur
- id (int, auto)
- nom (string, 255)
- prenom (string, 255)

### Entité Emprunt
- id (int, auto)
- utilisateur (relation ManyToOne vers Utilisateur)
- livre (relation ManyToOne vers Livre)
- dateEmprunt (datetime)
- dateRetourPrevu (date, nullable)
- dateRetourEffectif (datetime, nullable)

## Relations

- Un livre appartient à un auteur (ManyToOne)
- Un livre appartient à une catégorie (ManyToOne)
- Un emprunt est lié à un utilisateur et un livre (ManyToOne pour chaque)

## Règles métier

1. Un livre ne peut être emprunté que s'il est disponible
2. Un utilisateur ne peut pas emprunter plus de 4 livres simultanément
3. Un livre emprunté n'est plus disponible jusqu'à son retour
4. Un emprunt est considéré actif tant que `dateRetourEffectif` est NULL

## Routes API

### Livres (CRUD)

- `GET /api/livres` - Liste tous les livres
- `GET /api/livres/{id}` - Détails d'un livre
- `POST /api/livres` - Créer un livre
- `PUT /api/livres/{id}` - Modifier un livre
- `DELETE /api/livres/{id}` - Supprimer un livre

### Emprunts

- `POST /api/emprunts` - Emprunter un livre
- `PUT /api/emprunts/{id}/retour` - Retourner un livre
- `GET /api/utilisateurs/{id}/emprunts` - Liste des emprunts en cours d'un utilisateur (triés par date)
- `GET /api/auteurs/{id}/livres/empruntes` - Livres d'un auteur empruntés entre deux dates

## Format des données

Toutes les requêtes et réponses utilisent le format JSON.

### Exemple de requête - Emprunt d'un livre

```json
POST /api/emprunts
{
  "utilisateurId": 1,
  "livreId": 5,
  "dateRetourPrevu": "2025-11-15"
}
```

### Exemple de réponse - Succès

```json
HTTP/1.1 201 Created
{
  "id": 12,
  "utilisateur": {
    "id": 1,
    "nom": "Dupont",
    "prenom": "Jean"
  },
  "livre": {
    "id": 5,
    "titre": "Le Petit Prince"
  },
  "dateEmprunt": "2025-10-17T14:30:00+00:00",
  "dateRetourPrevu": "2025-11-15"
}
```

### Exemple de réponse - Erreur

```json
HTTP/1.1 400 Bad Request
{
  "error": "Ce livre n'est pas disponible"
}
```

## Codes HTTP utilisés

- `200 OK` - Requête réussie
- `201 Created` - Ressource créée avec succès
- `204 No Content` - Suppression réussie
- `400 Bad Request` - Données invalides
- `404 Not Found` - Ressource non trouvée
- `422 Unprocessable Entity` - Violation de règle métier
- `500 Internal Server Error` - Erreur serveur

## Développement

### Lancer le serveur de développement

```bash
symfony serve
```

ou

```bash
php -S localhost:8000 -t public
```

### Vérifier le code

```bash
# Analyse statique
vendor/bin/phpstan analyse src

# Standards de code
vendor/bin/php-cs-fixer fix
```

### Tests

```bash
php bin/phpunit
```

## Logs et débogage

Les logs de l'application se trouvent dans `var/log/`.

En cas d'erreur, l'API ne renvoie jamais d'informations sensibles. Consultez les logs pour le débogage détaillé.

## Sécurité

- Validation des types des paramètres d'URL
- Validation des données d'entrée avec le composant Validator de Symfony
- Contraintes HTTP sur les routes (GET, POST, PUT, DELETE)
- Pas d'exposition d'informations sensibles dans les réponses

## Gestion de version

Le projet utilise Git avec des commits réguliers après chaque fonctionnalité ajoutée :

```bash
git add .
git commit -m "feat: ajout du CRUD pour les livres"
git push origin main
```

## Technologies utilisées

- Symfony 6.4
- Doctrine ORM
- PHP 8.1+
- MySQL/PostgreSQL

## Auteur

[Votre nom]

## Licence

[Type de licence]
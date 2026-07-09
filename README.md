# Application Bibliotheque - TP5 Argent

Cette application permet de gerer une petite bibliotheque avec les fonctions du niveau Bronze et Argent.

## Fonctionnalites

- Connexion a une base MySQL avec PDO
- Affichage du catalogue des livres
- Affichage du detail d'un livre
- Recherche par titre ou auteur
- Ajout d'un nouveau livre
- Modification et suppression de livres
- Filtres par categorie, disponibilite et annee
- Connexion et deconnexion
- Sessions utilisateur
- Gestion des utilisateurs
- Desactivation d'un utilisateur
- Affichage des emprunts en cours
- Creation d'un emprunt
- Affichage des emprunts en retard
- Validation simple des champs
- Gestion de l'ISBN unique
- Gestion de l'email unique

## Installation

1. Demarrer WampServer.
2. Ouvrir phpMyAdmin.
3. Importer le fichier `database/schema.sql`.
4. Verifier les identifiants dans `config/database.php`.
5. Ouvrir le dossier `public` avec le serveur local.

Avec WampServer, l'utilisateur MySQL par defaut est souvent `root` avec un mot de passe vide.

## Structure

- `config/database.php` : connexion a la base de donnees
- `public/index.php` : page d'accueil
- `public/catalogue.php` : liste et recherche des livres
- `public/livre.php` : detail d'un livre
- `public/ajouter.php` : formulaire d'ajout
- `templates` : morceaux de pages reutilises
- `assets/css/style.css` : style de base
- `database/schema.sql` : base de donnees et donnees de test

## Base de donnees

La base s'appelle `bibliotheque_tp5`.

Elle contient quatre tables :

- `categories`
- `livres`
- `utilisateurs`
- `emprunts`

## Comptes de test

- `admin@example.com` / `admin123`
- `lecteur@example.com` / `user123`

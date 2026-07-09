# Application Bibliotheque - TP5 Or

Cette application permet de gerer une petite bibliotheque avec les fonctions des niveaux Bronze, Argent et Or.

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
- Retour de livre avec mise a jour du statut
- Reservations de livres indisponibles
- Annulation des reservations
- Notification simulee quand un livre reserve devient disponible
- Upload de couverture de livre
- Tableau de bord avec statistiques
- Pagination du catalogue
- Page des emprunts en retard
- Historique des emprunts par utilisateur
- Mise en avant de Blame!
- JavaScript pour rendre l'affichage plus dynamique

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
- `public/emprunts.php` : gestion des emprunts
- `public/reservations.php` : gestion des reservations
- `public/dashboard.php` : statistiques
- `public/retards.php` : liste des retards
- `templates` : morceaux de pages reutilises
- `assets/css/style.css` : style de base
- `assets/js/app.js` : petites animations JavaScript
- `uploads/couvertures` : couvertures ajoutees depuis le formulaire
- `database/schema.sql` : base de donnees et donnees de test

## Base de donnees

La base s'appelle `bibliotheque_tp5`.

Elle contient cinq tables :

- `categories`
- `livres`
- `utilisateurs`
- `emprunts`
- `reservations`

## Comptes de test

- `admin@example.com` / `admin123`
- `lecteur@example.com` / `user123`

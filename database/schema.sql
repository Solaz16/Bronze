CREATE DATABASE IF NOT EXISTS bibliotheque_tp5 CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE bibliotheque_tp5;

DROP TABLE IF EXISTS emprunts;
DROP TABLE IF EXISTS utilisateurs;
DROP TABLE IF EXISTS livres;
DROP TABLE IF EXISTS categories;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE livres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    auteur VARCHAR(150) NOT NULL,
    isbn VARCHAR(30) NOT NULL UNIQUE,
    annee_publication INT NULL,
    resume TEXT,
    categorie_id INT,
    disponible TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (categorie_id) REFERENCES categories(id)
);

CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    actif TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE emprunts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    livre_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    date_emprunt DATE NOT NULL,
    date_retour_prevue DATE NOT NULL,
    date_retour_effective DATE NULL,
    statut VARCHAR(30) NOT NULL DEFAULT 'en_cours',
    FOREIGN KEY (livre_id) REFERENCES livres(id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

INSERT INTO categories (nom) VALUES
('Roman'),
('Science-fiction'),
('Histoire'),
('Informatique'),
('Jeunesse');

INSERT INTO livres (titre, auteur, isbn, annee_publication, resume, categorie_id, disponible) VALUES
('Le Petit Prince', 'Antoine de Saint-Exupery', '9782070612758', 1943, 'Un pilote rencontre un jeune prince venu d une autre planete.', 5, 1),
('1984', 'George Orwell', '9782070368228', 1949, 'Un roman sur une societe surveillee par un pouvoir totalitaire.', 2, 1),
('Les Miserables', 'Victor Hugo', '9782253096344', 1862, 'L histoire de Jean Valjean dans la France du dix-neuvieme siecle.', 1, 0),
('Sapiens', 'Yuval Noah Harari', '9782226257017', 2011, 'Une histoire courte de l humanite.', 3, 1),
('Apprendre PHP', 'Jean Dupont', '9780000000001', 2022, 'Un livre simple pour commencer a programmer en PHP.', 4, 1);

INSERT INTO utilisateurs (nom, email, mot_de_passe, actif) VALUES
('Admin', 'admin@example.com', '$2y$12$kYaarICtO638MlAIu/ohXO0bHKQxA3o/JtLwLtVbV6ILaEsMEI96i', 1),
('Lecteur Test', 'lecteur@example.com', '$2y$12$hfMUAIsG0Zg21K5LHo/i6usOkJy52dr1hJv.X42e3ShdDaEvZpkdG', 1),
('Ancien Lecteur', 'ancien@example.com', '$2y$12$hfMUAIsG0Zg21K5LHo/i6usOkJy52dr1hJv.X42e3ShdDaEvZpkdG', 0);

INSERT INTO emprunts (livre_id, utilisateur_id, date_emprunt, date_retour_prevue, statut) VALUES
(3, 2, '2026-06-20', '2026-07-04', 'en_cours');

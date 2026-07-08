CREATE DATABASE IF NOT EXISTS bibliotheque_tp5 CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE bibliotheque_tp5;

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

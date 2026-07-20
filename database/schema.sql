CREATE DATABASE IF NOT EXISTS bibliotheque_tp5 CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE bibliotheque_tp5;

DROP TABLE IF EXISTS emprunts;
DROP TABLE IF EXISTS reservations;
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
    couverture VARCHAR(255) NULL,
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

CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    livre_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    date_reservation DATE NOT NULL,
    statut VARCHAR(30) NOT NULL DEFAULT 'en_attente',
    FOREIGN KEY (livre_id) REFERENCES livres(id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

INSERT INTO categories (nom) VALUES
('Seinen'),
('Cyberpunk'),
('Dark fantasy'),
('Thriller'),
('Historique'),
('Horreur'),
('Psychologique'),
('Science-fiction'),
('Drame'),
('Aventure');

INSERT INTO livres (titre, auteur, isbn, annee_publication, resume, categorie_id, disponible) VALUES
('Blame!', 'Tsutomu Nihei', '9782723496447', 1997, 'Killy traverse une immense megastructure pour retrouver un gene rare.', 2, 1),
('Berserk', 'Kentaro Miura', '9782723422880', 1989, 'Guts lutte dans un monde sombre marque par la violence et le destin.', 3, 0),
('Vagabond', 'Takehiko Inoue', '9782845800665', 1998, 'Une version manga de la vie de Miyamoto Musashi.', 5, 1),
('Monster', 'Naoki Urasawa', '9782505001883', 1994, 'Un chirurgien poursuit un ancien patient devenu tueur.', 4, 1),
('20th Century Boys', 'Naoki Urasawa', '9782809401853', 1999, 'Un groupe d amis affronte une secte liee a leur enfance.', 4, 1),
('Pluto', 'Naoki Urasawa', '9782505004624', 2003, 'Une relecture sombre et policiere de l univers d Astro Boy.', 4, 1),
('Goodnight Punpun', 'Inio Asano', '9782505063829', 2007, 'Le parcours difficile de Punpun entre enfance, amour et mal-etre.', 1, 0),
('Solanin', 'Inio Asano', '9782505007717', 2005, 'De jeunes adultes cherchent leur place apres les etudes.', 1, 1),
('Dead Dead Demon''s Dededede Destruction', 'Inio Asano', '9782505071459', 2014, 'Deux lyceennes vivent leur quotidien sous un vaisseau alien.', 1, 1),
('Vinland Saga', 'Makoto Yukimura', '9782368520009', 2005, 'Une fresque viking autour de la vengeance et de la paix.', 5, 1),
('Kingdom', 'Yasuhisa Hara', '9782368526575', 2006, 'Deux orphelins veulent marquer l histoire de la Chine antique.', 5, 1),
('Dorohedoro', 'Q Hayashida', '9782302008191', 2000, 'Caiman cherche qui lui a donne une tete de reptile.', 3, 1),
('Gantz', 'Hiroya Oku', '9782845802270', 2000, 'Des morts sont forces de combattre des creatures inconnues.', 1, 0),
('Akira', 'Katsuhiro Otomo', '9782723428264', 1982, 'Neo-Tokyo sombre dans le chaos apres le reveil de pouvoirs terrifiants.', 2, 1),
('Ghost in the Shell', 'Masamune Shirow', '9782723423542', 1989, 'Le major Kusanagi enquete dans un futur cybernetique.', 2, 1),
('Eden: It''s an Endless World!', 'Hiroki Endo', '9782203373013', 1998, 'Un monde post-pandemie entre mafia, politique et survie.', 2, 1),
('Biomega', 'Tsutomu Nihei', '9782723465146', 2004, 'Un agent synthetique traverse un monde contamine.', 2, 1),
('Noise', 'Tsutomu Nihei', '9782723442888', 2000, 'Un court recit dans le meme univers sombre que Blame!.', 2, 1),
('Knights of Sidonia', 'Tsutomu Nihei', '9782723496454', 2009, 'Des humains survivent dans un vaisseau face aux Gauna.', 2, 1),
('Homunculus', 'Hideo Yamamoto', '9782759500361', 2003, 'Un homme subit une trepanation et voit les formes cachees des gens.', 4, 1),
('Ichi the Killer', 'Hideo Yamamoto', '9782845804083', 1998, 'Un recit violent autour de yakuzas et de manipulations.', 4, 0),
('Lone Wolf and Cub', 'Kazuo Koike', '9781593075083', 1970, 'Un ancien bourreau voyage avec son fils dans le Japon feodal.', 5, 1),
('The Climber', 'Shinichi Sakamoto', '9782759508565', 2007, 'Un lyceen solitaire se decouvre une passion pour l escalade.', 1, 1),
('Innocent', 'Shinichi Sakamoto', '9782756071918', 2013, 'L histoire d une famille de bourreaux avant la Revolution francaise.', 5, 1),
('Real', 'Takehiko Inoue', '9782505003214', 1999, 'Trois personnages se reconstruisent autour du basket en fauteuil.', 1, 1),
('Planetes', 'Makoto Yukimura', '9782809404359', 1999, 'Des eboueurs de l espace questionnent leur avenir et leurs reves.', 2, 1),
('The Fable', 'Katsuhisa Minami', '9782811668152', 2014, 'Un tueur professionnel doit vivre une annee comme un civil ordinaire.', 1, 1),
('Ajin', 'Gamon Sakurai', '9782344006589', 2012, 'Des humains immortels sont pourchasses par le gouvernement.', 4, 1),
('Parasyte', 'Hitoshi Iwaaki', '9782723444691', 1988, 'Un lyceen cohabite avec une creature parasite dans sa main.', 1, 1),
('Hellsing', 'Kouta Hirano', '9782845800801', 1997, 'Une organisation anglaise combat vampires et monstres.', 3, 1),
('Black Lagoon', 'Rei Hiroe', '9782845805394', 2002, 'Des mercenaires transportent des cargaisons dangereuses en Asie.', 1, 1),
('Tokyo Ghoul', 'Sui Ishida', '9782723499691', 2011, 'Kaneki devient mi-humain mi-goule apres un accident.', 3, 1),
('Bokurano', 'Mohiro Kitoh', '9782845807824', 2003, 'Des enfants pilotent un robot geant avec un prix terrible.', 2, 1),
('Devilman', 'Go Nagai', '9782372872071', 1972, 'Akira devient Devilman pour affronter les demons.', 3, 1),
('Girls'' Last Tour', 'Tsukumizu', '9780316470636', 2014, 'Deux filles traversent les ruines silencieuses de la civilisation.', 1, 1);

INSERT INTO livres (titre, auteur, isbn, annee_publication, resume, categorie_id, disponible) VALUES
('Blade of the Immortal', 'Hiroaki Samura', '9780003000001', 1993, 'Un ronin maudit traverse les siecles en cherchant la redemption.', 5, 1),
('Shigurui', 'Takayuki Yamaguchi', '9780003000002', 2003, 'Un duel d honneur plonge le Japon feodal dans une violence glaciale.', 5, 0),
('A Bride''s Story', 'Kaoru Mori', '9780003000003', 2008, 'Une histoire de mariage et de quotidien dans l Asie centrale du XIXe siecle.', 5, 1),
('Golden Kamuy', 'Satoru Noda', '9780003000004', 2014, 'Une chasse au tresor brutale se deroule dans un Hokkaido dur et sauvage.', 5, 1),
('Mushishi', 'Yuki Urushibara', '9780003000005', 2000, 'Un vagabond enquete sur des phenomenes discrets lies aux mushi.', 1, 1),
('The Drifting Classroom', 'Kazuo Umezu', '9780003000006', 1972, 'Une ecole entiere se retrouve projetee dans un monde de cauchemar.', 6, 0),
('Uzumaki', 'Junji Ito', '9780003000007', 1998, 'Une ville entiere bascule dans une obsession macabre autour des spirales.', 6, 0),
('Gyo', 'Junji Ito', '9780003000008', 2001, 'Des creatures mecaniques et une odeur pestilentielle envahissent le rivage.', 6, 0),
('Tomie', 'Junji Ito', '9780003000009', 1987, 'Une jeune femme insaisissable nourrit autour d elle desir et destruction.', 6, 0),
('Remina', 'Junji Ito', '9780003000010', 2005, 'Une planete inconnue attire la peur et la folie a l echelle mondiale.', 6, 0),
('Sensor', 'Junji Ito', '9780003000011', 2018, 'Des visions etranges relient une montagne isolee a un phenomene cosmique.', 6, 1),
('Franken Fran', 'Katsuhisa Kigitsu', '9780003000012', 2006, 'Une chirurgienne monstrueusement brillante enchaine les experiences absurdes.', 6, 1),
('I Am a Hero', 'Kengo Hanazawa', '9780003000013', 2009, 'Un assistant mangaka survit a un effondrement zombie de plus en plus sourd.', 6, 0),
('Under Ninja', 'Kengo Hanazawa', '9780003000014', 2018, 'Des ninjas invisibles continuent de manipuler le Japon contemporain.', 4, 1),
('Blood on the Tracks', 'Shuzo Oshimi', '9780003000015', 2017, 'Une relation mere-fils derive vers une tension psychologique insoutenable.', 7, 1),
('Happiness', 'Shuzo Oshimi', '9780003000016', 2015, 'Un adolescent subit une metamorphose qui l eloigne peu a peu du monde humain.', 6, 0),
('The Flowers of Evil', 'Shuzo Oshimi', '9780003000017', 2009, 'Un collegien glisse dans l obsession, la honte et l auto-destruction.', 7, 1),
('Welcome Back, Alice', 'Shuzo Oshimi', '9780003000018', 2020, 'Trois adolescents confrontent une identite mouvante et des desirs confus.', 7, 1),
('Dragon Head', 'Minetaro Mochizuki', '9780003000019', 1994, 'Apres un cataclysme, un groupe de survivants derive dans un huis clos de peur.', 4, 0),
('Billy Bat', 'Naoki Urasawa', '9780003000020', 2008, 'Un dessinateur decouvre que son heros de papier manipule une histoire plus vaste.', 4, 1),
('Sunny', 'Taiyo Matsumoto', '9780003000021', 2006, 'Des enfants trouvent un refuge fragile dans une voiture abandonnee.', 9, 1),
('Tekkonkinkreet', 'Taiyo Matsumoto', '9780003000022', 1993, 'Deux garcons protegent leur ville contre des forces qui la devorent de l interieur.', 1, 1),
('Ping Pong', 'Taiyo Matsumoto', '9780003000023', 1996, 'Des adolescents se cherchent a travers l intensite du sport et du doute.', 9, 1),
('Number Five', 'Taiyo Matsumoto', '9780003000024', 2004, 'Des agents d elite naviguent dans une mission aussi politique qu onirique.', 10, 1),
('A Distant Neighborhood', 'Jiro Taniguchi', '9780003000025', 1998, 'Un adulte retourne dans son adolescence et affronte ses choix avec calme.', 9, 1),
('The Walking Man', 'Jiro Taniguchi', '9780003000026', 1992, 'Une promenade sans but devient une meditation douce sur le temps et la ville.', 9, 1),
('Summit of the Gods', 'Jiro Taniguchi', '9780003000027', 2000, 'L obsession de l alpinisme devient une quete d absolu et de silence.', 10, 1),
('Battle Angel Alita', 'Yukito Kishiro', '9780003000028', 1990, 'Une cyborg amnesique remonte les strates d un futur dur et metallique.', 2, 1),
('No Guns Life', 'Tasuku Karasuma', '9780003000029', 2014, 'Un enqueteur au crane transforme traine dans une ville saturee de machines.', 2, 1),
('Origin', 'Boichi', '9780003000030', 2016, 'Un androide protege un secret dans un Tokyo sature de haute technologie.', 2, 1),
('Aposimz', 'Tsutomu Nihei', '9780003000031', 2017, 'Un monde glace et mecanise cache des ruines plus anciennes qu il n y parait.', 8, 1),
('Abara', 'Tsutomu Nihei', '9780003000032', 2005, 'Un survivant monstrueux traverse des structures minerales et hostiles.', 2, 0),
('Dai Dark', 'Q Hayashida', '9780003000033', 2019, 'Une chasse aux ossements sacres entraine un trio dans l espace le plus sale.', 3, 1),
('Claymore', 'Norihiro Yagi', '9780003000034', 2001, 'Des guerrieres hybrides affrontent des monstres dans un monde froid et brutal.', 3, 1),
('Hell''s Paradise: Jigokuraku', 'Yuji Kaku', '9780003000035', 2018, 'Des criminels cherchent l immortalite sur une ile luxuriante et mortelle.', 3, 1);

INSERT INTO utilisateurs (nom, email, mot_de_passe, actif) VALUES
('Admin', 'admin@example.com', '$2y$12$kYaarICtO638MlAIu/ohXO0bHKQxA3o/JtLwLtVbV6ILaEsMEI96i', 1),
('Lecteur Test', 'lecteur@example.com', '$2y$12$hfMUAIsG0Zg21K5LHo/i6usOkJy52dr1hJv.X42e3ShdDaEvZpkdG', 1),
('Ancien Lecteur', 'ancien@example.com', '$2y$12$hfMUAIsG0Zg21K5LHo/i6usOkJy52dr1hJv.X42e3ShdDaEvZpkdG', 0);

INSERT INTO emprunts (livre_id, utilisateur_id, date_emprunt, date_retour_prevue, statut) VALUES
(2, 2, '2026-06-20', '2026-07-04', 'en_cours'),
(7, 2, '2026-07-01', '2026-07-15', 'en_cours'),
(13, 1, '2026-07-05', '2026-07-19', 'en_cours');

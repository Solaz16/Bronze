SET NAMES utf8mb4;

INSERT IGNORE INTO categories (nom) VALUES
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

INSERT IGNORE INTO livres (titre, auteur, isbn, annee_publication, resume, categorie_id, disponible)
SELECT source.titre, source.auteur, source.isbn, source.annee_publication, source.resume_texte, c.id, source.disponible
FROM (
    SELECT 'Blade of the Immortal' AS titre, 'Hiroaki Samura' AS auteur, '9780003000001' AS isbn, 1993 AS annee_publication, 'Un rônin maudit traverse les siècles en cherchant la rédemption.' AS resume_texte, 'Historique' AS categorie_nom, 1 AS disponible
    UNION ALL SELECT 'Shigurui', 'Takayuki Yamaguchi', '9780003000002', 2003, 'Un duel d honneur plonge le Japon féodal dans une violence glaciale.', 'Historique', 0
    UNION ALL SELECT 'A Bride''s Story', 'Kaoru Mori', '9780003000003', 2008, 'Une histoire de mariage et de quotidien dans l Asie centrale du XIXe siècle.', 'Historique', 1
    UNION ALL SELECT 'Golden Kamuy', 'Satoru Noda', '9780003000004', 2014, 'Une chasse au trésor brutale se déroule dans un Hokkaido dur et sauvage.', 'Historique', 1
    UNION ALL SELECT 'Mushishi', 'Yuki Urushibara', '9780003000005', 2000, 'Un vagabond enquête sur des phénomènes discrets liés aux mushi.', 'Seinen', 1
    UNION ALL SELECT 'The Drifting Classroom', 'Kazuo Umezu', '9780003000006', 1972, 'Une école entière se retrouve projetée dans un monde de cauchemar.', 'Horreur', 0
    UNION ALL SELECT 'Uzumaki', 'Junji Ito', '9780003000007', 1998, 'Une ville entière bascule dans une obsession macabre autour des spirales.', 'Horreur', 0
    UNION ALL SELECT 'Gyo', 'Junji Ito', '9780003000008', 2001, 'Des créatures mécaniques et une odeur pestilentielle envahissent le rivage.', 'Horreur', 0
    UNION ALL SELECT 'Tomie', 'Junji Ito', '9780003000009', 1987, 'Une jeune femme insaisissable nourrit autour d elle désir et destruction.', 'Horreur', 0
    UNION ALL SELECT 'Remina', 'Junji Ito', '9780003000010', 2005, 'Une planète inconnue attire la peur et la folie à l échelle mondiale.', 'Horreur', 0
    UNION ALL SELECT 'Sensor', 'Junji Ito', '9780003000011', 2018, 'Des visions étranges relient une montagne isolée à un phénomène cosmique.', 'Horreur', 1
    UNION ALL SELECT 'Franken Fran', 'Katsuhisa Kigitsu', '9780003000012', 2006, 'Une chirurgienne monstrueusement brillante enchaîne les expériences absurdes.', 'Horreur', 1
    UNION ALL SELECT 'I Am a Hero', 'Kengo Hanazawa', '9780003000013', 2009, 'Un assistant mangaka survit à un effondrement zombie de plus en plus sourd.', 'Horreur', 0
    UNION ALL SELECT 'Under Ninja', 'Kengo Hanazawa', '9780003000014', 2018, 'Des ninjas invisibles continuent de manipuler le Japon contemporain.', 'Thriller', 1
    UNION ALL SELECT 'Blood on the Tracks', 'Shuzo Oshimi', '9780003000015', 2017, 'Une relation mère-fils dérive vers une tension psychologique insoutenable.', 'Psychologique', 1
    UNION ALL SELECT 'Happiness', 'Shuzo Oshimi', '9780003000016', 2015, 'Un adolescent subit une métamorphose qui l éloigne peu à peu du monde humain.', 'Horreur', 0
    UNION ALL SELECT 'The Flowers of Evil', 'Shuzo Oshimi', '9780003000017', 2009, 'Un collégien glisse dans l obsession, la honte et l auto-destruction.', 'Psychologique', 1
    UNION ALL SELECT 'Welcome Back, Alice', 'Shuzo Oshimi', '9780003000018', 2020, 'Trois adolescents confrontent une identité mouvante et des désirs confus.', 'Psychologique', 1
    UNION ALL SELECT 'Dragon Head', 'Minetaro Mochizuki', '9780003000019', 1994, 'Après un cataclysme, un groupe de survivants dérive dans un huis clos de peur.', 'Thriller', 0
    UNION ALL SELECT 'Billy Bat', 'Naoki Urasawa', '9780003000020', 2008, 'Un dessinateur découvre que son héros de papier manipule une histoire plus vaste.', 'Thriller', 1
    UNION ALL SELECT 'Sunny', 'Taiyo Matsumoto', '9780003000021', 2006, 'Des enfants trouvent un refuge fragile dans une voiture abandonnée.', 'Drame', 1
    UNION ALL SELECT 'Tekkonkinkreet', 'Taiyo Matsumoto', '9780003000022', 1993, 'Deux garçons protègent leur ville contre des forces qui la dévorent de l interieur.', 'Seinen', 1
    UNION ALL SELECT 'Ping Pong', 'Taiyo Matsumoto', '9780003000023', 1996, 'Des adolescents se cherchent à travers l intensité du sport et du doute.', 'Drame', 1
    UNION ALL SELECT 'Number Five', 'Taiyo Matsumoto', '9780003000024', 2004, 'Des agents d élite naviguent dans une mission aussi politique qu onirique.', 'Aventure', 1
    UNION ALL SELECT 'A Distant Neighborhood', 'Jiro Taniguchi', '9780003000025', 1998, 'Un adulte retourne dans son adolescence et affronte ses choix avec calme.', 'Drame', 1
    UNION ALL SELECT 'The Walking Man', 'Jiro Taniguchi', '9780003000026', 1992, 'Une promenade sans but devient une méditation douce sur le temps et la ville.', 'Drame', 1
    UNION ALL SELECT 'Summit of the Gods', 'Jiro Taniguchi', '9780003000027', 2000, 'L obsession de l alpinisme devient une quête d absolu et de silence.', 'Aventure', 1
    UNION ALL SELECT 'Battle Angel Alita', 'Yukito Kishiro', '9780003000028', 1990, 'Une cyborg amnésique remonte les strates d un futur dur et métallique.', 'Cyberpunk', 1
    UNION ALL SELECT 'No Guns Life', 'Tasuku Karasuma', '9780003000029', 2014, 'Un enquêteur au crâne transformé traîne dans une ville saturée de machines.', 'Cyberpunk', 1
    UNION ALL SELECT 'Origin', 'Boichi', '9780003000030', 2016, 'Un androïde protège un secret dans un Tokyo saturé de haute technologie.', 'Cyberpunk', 1
    UNION ALL SELECT 'Aposimz', 'Tsutomu Nihei', '9780003000031', 2017, 'Un monde glacé et mécanisé cache des ruines plus anciennes qu il n y paraît.', 'Science-fiction', 1
    UNION ALL SELECT 'Abara', 'Tsutomu Nihei', '9780003000032', 2005, 'Un survivant monstrueux traverse des structures minérales et hostiles.', 'Cyberpunk', 0
    UNION ALL SELECT 'Dai Dark', 'Q Hayashida', '9780003000033', 2019, 'Une chasse aux ossements sacrés entraîne un trio dans l espace le plus sale.', 'Dark fantasy', 1
    UNION ALL SELECT 'Claymore', 'Norihiro Yagi', '9780003000034', 2001, 'Des guerrières hybrides affrontent des monstres dans un monde froid et brutal.', 'Dark fantasy', 1
    UNION ALL SELECT 'Hell''s Paradise: Jigokuraku', 'Yuji Kaku', '9780003000035', 2018, 'Des criminels cherchent l immortalité sur une île luxuriante et mortelle.', 'Dark fantasy', 1
) AS source
LEFT JOIN categories c ON c.nom = source.categorie_nom;
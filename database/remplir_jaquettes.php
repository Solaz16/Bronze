<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/jaquettes.php';

$pdo = connexionBDD();
$livres = $pdo->query("SELECT id, titre, auteur, couverture FROM livres ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
$requete = $pdo->prepare("UPDATE livres SET couverture = :couverture WHERE id = :id");

foreach ($livres as $livre) {
    if (!empty($livre['couverture'])) {
        echo $livre['titre'] . " : deja definie\n";
        continue;
    }

    $couverture = jaquetteLivreAutomatique($livre['titre'], '', $livre['auteur'] ?? '');

    if ($couverture === '') {
        echo $livre['titre'] . " : aucune jaquette trouvee\n";
        continue;
    }

    $requete->execute([
        'couverture' => $couverture,
        'id' => $livre['id']
    ]);

    echo $livre['titre'] . " : jaquette ajoutee\n";
    usleep(350000);
}
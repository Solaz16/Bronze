<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/jaquettes.php';

header('Content-Type: application/json; charset=utf-8');
$pdo = connexionBDD();
$livres = $pdo->query("SELECT livres.id, livres.titre, livres.auteur, livres.couverture, categories.nom AS categorie FROM livres LEFT JOIN categories ON livres.categorie_id = categories.id ORDER BY livres.titre")->fetchAll(PDO::FETCH_ASSOC);

foreach ($livres as &$livre) {
    $livre['couverture'] = jaquetteLivre($livre['titre'], $livre['couverture'] ?? '', $livre['auteur'] ?? '');
    $livre['categorie'] = $livre['categorie'] ?? 'Non classe';
}

echo json_encode($livres, JSON_UNESCAPED_UNICODE);

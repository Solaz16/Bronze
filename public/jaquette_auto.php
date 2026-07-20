<?php
require_once __DIR__ . '/../config/jaquettes.php';

header('Content-Type: application/json; charset=utf-8');

$titre = trim($_GET['titre'] ?? '');
$auteur = trim($_GET['auteur'] ?? '');

if ($titre === '') {
    echo json_encode(['ok' => false, 'message' => 'Titre manquant'], JSON_UNESCAPED_UNICODE);
    exit;
}

$url = jaquetteLivre($titre, '', $auteur);

echo json_encode([
    'ok' => $url !== '',
    'url' => $url,
    'source' => str_starts_with($url, 'https://covers.openlibrary.org/') ? 'openlibrary' : 'cache'
], JSON_UNESCAPED_UNICODE);
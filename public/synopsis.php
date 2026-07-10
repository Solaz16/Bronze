<?php
header('Content-Type: application/json; charset=utf-8');

$titre = trim($_GET['titre'] ?? '');

if ($titre === '') {
    echo json_encode(['ok' => false, 'message' => 'Titre manquant.']);
    exit;
}

$url = 'https://api.jikan.moe/v4/manga?q=' . urlencode($titre) . '&limit=1';
$contexte = stream_context_create([
    'http' => [
        'timeout' => 8,
        'header' => "User-Agent: BibliothequeTP5/1.0\r\n"
    ]
]);

$reponse = @file_get_contents($url, false, $contexte);

if ($reponse === false) {
    echo json_encode(['ok' => false, 'message' => 'Impossible de joindre l API.']);
    exit;
}

$data = json_decode($reponse, true);
$manga = $data['data'][0] ?? null;
$synopsis = trim($manga['synopsis'] ?? '');

if (!$manga || $synopsis === '') {
    echo json_encode(['ok' => false, 'message' => 'Aucun synopsis trouve.']);
    exit;
}

$synopsis = preg_replace('/\s*\[Written by MAL Rewrite\]\s*/', '', $synopsis);
$synopsis = preg_replace('/\s*\(Source:.*?\)\s*/', '', $synopsis);
$synopsis = trim($synopsis);

echo json_encode([
    'ok' => true,
    'titre' => $manga['title'] ?? $titre,
    'synopsis' => $synopsis
]);

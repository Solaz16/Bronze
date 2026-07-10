<?php
header('Content-Type: application/json; charset=utf-8');

$titre = trim($_GET['titre'] ?? '');

function appelApi($url)
{
    $contexte = stream_context_create([
        'http' => [
            'timeout' => 10,
            'header' => "User-Agent: BibliothequeTP5/1.0\r\n"
        ]
    ]);

    return @file_get_contents($url, false, $contexte);
}

function nettoyerSynopsis($texte)
{
    $texte = preg_replace('/\s*\[Written by MAL Rewrite\]\s*/', '', $texte);
    $texte = preg_replace('/\s*\(Source:.*?\)\s*/', '', $texte);
    return trim($texte);
}

function couperTexte($texte)
{
    $phrases = preg_split('/(?<=[.!?])\s+/', $texte);
    $morceaux = [];
    $bloc = '';

    foreach ($phrases as $phrase) {
        if (strlen($bloc . ' ' . $phrase) > 450 && $bloc !== '') {
            $morceaux[] = trim($bloc);
            $bloc = '';
        }

        $bloc .= ' ' . $phrase;
    }

    if (trim($bloc) !== '') {
        $morceaux[] = trim($bloc);
    }

    return $morceaux;
}

function traduireEnFrancais($texte)
{
    $traductions = [];

    foreach (couperTexte($texte) as $morceau) {
        $url = 'https://api.mymemory.translated.net/get?q=' . rawurlencode($morceau) . '&langpair=en|fr';
        $reponse = appelApi($url);

        if ($reponse === false) {
            return $texte;
        }

        $data = json_decode($reponse, true);
        $traduit = trim($data['responseData']['translatedText'] ?? '');

        if ($traduit === '') {
            return $texte;
        }

        $traductions[] = html_entity_decode($traduit, ENT_QUOTES, 'UTF-8');
    }

    return trim(implode(' ', $traductions));
}

if ($titre === '') {
    echo json_encode(['ok' => false, 'message' => 'Titre manquant.']);
    exit;
}

$url = 'https://api.jikan.moe/v4/manga?q=' . urlencode($titre) . '&limit=1';
$reponse = appelApi($url);

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

$synopsis = nettoyerSynopsis($synopsis);
$synopsis = traduireEnFrancais($synopsis);

echo json_encode([
    'ok' => true,
    'titre' => $manga['title'] ?? $titre,
    'langue' => 'fr',
    'synopsis' => $synopsis
]);

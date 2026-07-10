<?php
require_once __DIR__ . '/../config/database.php';

function appelApi($url)
{
    $contexte = stream_context_create([
        'http' => [
            'timeout' => 12,
            'header' => "User-Agent: BibliothequeTP5/1.0\r\n"
        ]
    ]);

    return @file_get_contents($url, false, $contexte);
}

function nettoyerTexte($texte)
{
    $texte = preg_replace('/\s*\[Written by MAL Rewrite\]\s*/', '', $texte);
    $texte = preg_replace('/\s*\(Source:.*?\)\s*/', '', $texte);
    $texte = str_replace(["\r", "\n"], ' ', $texte);
    $texte = preg_replace('/\s+/', ' ', $texte);
    return trim($texte);
}

function morceaux($texte)
{
    $phrases = preg_split('/(?<=[.!?])\s+/', $texte);
    $liste = [];
    $bloc = '';

    foreach ($phrases as $phrase) {
        if (strlen($bloc . ' ' . $phrase) > 450 && $bloc !== '') {
            $liste[] = trim($bloc);
            $bloc = '';
        }

        $bloc .= ' ' . $phrase;
    }

    if (trim($bloc) !== '') {
        $liste[] = trim($bloc);
    }

    return $liste;
}

function traduire($texte)
{
    $urlGoogle = 'https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=fr&dt=t&q=' . rawurlencode($texte);
    $reponseGoogle = appelApi($urlGoogle);

    if ($reponseGoogle !== false) {
        $dataGoogle = json_decode($reponseGoogle, true);
        $phrases = $dataGoogle[0] ?? [];
        $resultatGoogle = '';

        foreach ($phrases as $phrase) {
            $resultatGoogle .= $phrase[0] ?? '';
        }

        if (trim($resultatGoogle) !== '') {
            return nettoyerTexte(html_entity_decode($resultatGoogle, ENT_QUOTES, 'UTF-8'));
        }
    }

    $resultat = [];

    foreach (morceaux($texte) as $morceau) {
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

        $resultat[] = html_entity_decode($traduit, ENT_QUOTES, 'UTF-8');
    }

    return nettoyerTexte(implode(' ', $resultat));
}

function trouverSynopsis($titre)
{
    $url = 'https://api.jikan.moe/v4/manga?q=' . urlencode($titre) . '&limit=1';
    $reponse = appelApi($url);

    if ($reponse === false) {
        return '';
    }

    $data = json_decode($reponse, true);
    $synopsis = nettoyerTexte($data['data'][0]['synopsis'] ?? '');

    if ($synopsis === '') {
        return '';
    }

    return traduire($synopsis);
}

$pdo = connexionBDD();
$livres = $pdo->query("SELECT id, titre FROM livres ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
$requete = $pdo->prepare("UPDATE livres SET resume = :resume WHERE id = :id");
$resumesConnus = [
    'Innocent' => "Dans la France du XVIIIe siecle, Charles-Henri Sanson grandit dans une famille chargee d'executer les condamnes. Entre devoir familial, violence sociale et envie de rester humain, il avance vers un destin lie aux heures les plus sombres de la Revolution.",
    'Hellsing' => "L'organisation Hellsing protege l'Angleterre contre les vampires et les creatures surnaturelles. Son arme la plus dangereuse est Alucard, un vampire surpuissant qui traque les monstres avec une brutalite froide et un humour inquietant.",
    'Noise' => "Dans un futur sombre relie a l'univers de Blame!, une policiere enquete sur des disparitions et sur les premieres traces d'un monde controle par les machines. L'histoire montre les origines de la Megastructure et de la menace qui devore peu a peu l'humanite.",
    'Tokyo Ghoul' => "Ken Kaneki, etudiant discret, survit a une attaque de goule et devient lui-meme mi-humain mi-goule. Coince entre deux mondes, il doit apprendre a survivre dans une societe cachee ou la faim, la peur et la violence dominent.",
    'berdly bizzare adventure' => "Une entree ajoutee a la main, pensee comme un delire autour d'une aventure improbable et dramatique. Le livre garde un ton volontairement absurde, entre reference de fan et fausse epopee beaucoup trop serieuse pour son propre bien."
];

foreach ($livres as $livre) {
    $resume = $resumesConnus[$livre['titre']] ?? trouverSynopsis($livre['titre']);

    if ($resume === '') {
        echo $livre['titre'] . " : ignore\n";
        continue;
    }

    $requete->execute([
        'resume' => $resume,
        'id' => $livre['id']
    ]);

    echo $livre['titre'] . " : ok\n";
    sleep(1);
}

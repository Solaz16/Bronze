<?php

function appelApiJaquette($url)
{
    $contexte = stream_context_create([
        'http' => [
            'timeout' => 8,
            'header' => "User-Agent: BibliothequeBronze/1.0 (Solaz16@users.noreply.github.com)\r\n"
        ]
    ]);

    return @file_get_contents($url, false, $contexte);
}

function cleJaquette($titre, $auteur = '')
{
    $cle = trim($titre . '|' . $auteur);

    if (function_exists('mb_strtolower')) {
        return mb_strtolower($cle, 'UTF-8');
    }

    return strtolower($cle);
}

function jaquetteOpenLibrary($titre, $auteur = '')
{
    static $cache = [];

    $cle = cleJaquette($titre, $auteur);

    if (array_key_exists($cle, $cache)) {
        return $cache[$cle];
    }

    $parametres = [
        'title' => $titre,
        'limit' => 5
    ];

    if ($auteur !== '') {
        $parametres['author'] = $auteur;
    }

    $url = 'https://openlibrary.org/search.json?' . http_build_query($parametres);
    $reponse = appelApiJaquette($url);

    if ($reponse === false) {
        $cache[$cle] = '';
        return '';
    }

    $data = json_decode($reponse, true);
    $documents = $data['docs'] ?? [];

    foreach ($documents as $document) {
        if (!empty($document['cover_i'])) {
            $cache[$cle] = 'https://covers.openlibrary.org/b/id/' . rawurlencode((string) $document['cover_i']) . '-L.jpg?default=false';
            return $cache[$cle];
        }

        if (!empty($document['cover_edition_key'])) {
            $cache[$cle] = 'https://covers.openlibrary.org/b/olid/' . rawurlencode((string) $document['cover_edition_key']) . '-L.jpg?default=false';
            return $cache[$cle];
        }
    }

    $cache[$cle] = '';
    return '';
}

function jaquetteGoogleBooks($titre, $auteur = '')
{
    static $cache = [];

    $cle = 'google:' . cleJaquette($titre, $auteur);

    if (array_key_exists($cle, $cache)) {
        return $cache[$cle];
    }

        $recherche = 'intitle:"' . $titre . '"';

    if ($auteur !== '') {
            $recherche .= ' inauthor:"' . $auteur . '"';
    }

    $url = 'https://www.googleapis.com/books/v1/volumes?q=' . rawurlencode($recherche) . '&maxResults=5';
    $reponse = appelApiJaquette($url);

    if ($reponse === false) {
        $cache[$cle] = '';
        return '';
    }

    $data = json_decode($reponse, true);
    $items = $data['items'] ?? [];

    foreach ($items as $item) {
        $images = $item['volumeInfo']['imageLinks'] ?? [];

        if (!empty($images['thumbnail'])) {
            $cache[$cle] = str_replace('http://', 'https://', $images['thumbnail']);
            return $cache[$cle];
        }

        if (!empty($images['smallThumbnail'])) {
            $cache[$cle] = str_replace('http://', 'https://', $images['smallThumbnail']);
            return $cache[$cle];
        }
    }

    $cache[$cle] = '';
    return '';
}

function jaquetteJikan($titre)
{
    static $cache = [];

    $cle = 'jikan:' . cleJaquette($titre);

    if (array_key_exists($cle, $cache)) {
        return $cache[$cle];
    }

    $url = 'https://api.jikan.moe/v4/manga?q=' . rawurlencode($titre) . '&limit=5';
    $reponse = appelApiJaquette($url);

    if ($reponse === false) {
        $cache[$cle] = '';
        return '';
    }

    $data = json_decode($reponse, true);
    $items = $data['data'] ?? [];

    foreach ($items as $item) {
        $images = $item['images'] ?? [];

        if (!empty($images['jpg']['large_image_url'])) {
            $cache[$cle] = $images['jpg']['large_image_url'];
            return $cache[$cle];
        }

        if (!empty($images['webp']['large_image_url'])) {
            $cache[$cle] = $images['webp']['large_image_url'];
            return $cache[$cle];
        }
    }

    $cache[$cle] = '';
    return '';
}

function jaquetteFallbackLocale($titre, $auteur = '')
{
    $texteTitre = trim($titre) !== '' ? trim($titre) : 'Jaquette manquante';
    $texteAuteur = trim($auteur) !== '' ? trim($auteur) : 'Fallback automatique';

    if (function_exists('mb_substr')) {
        $texteTitre = mb_substr($texteTitre, 0, 42, 'UTF-8');
        $texteAuteur = mb_substr($texteAuteur, 0, 30, 'UTF-8');
    } else {
        $texteTitre = substr($texteTitre, 0, 42);
        $texteAuteur = substr($texteAuteur, 0, 30);
    }

    $texteTitre = htmlspecialchars($texteTitre, ENT_QUOTES | ENT_XML1, 'UTF-8');
    $texteAuteur = htmlspecialchars($texteAuteur, ENT_QUOTES | ENT_XML1, 'UTF-8');

    $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="640" height="920" viewBox="0 0 640 920">
    <defs>
        <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#0d1724" />
            <stop offset="52%" stop-color="#101826" />
            <stop offset="100%" stop-color="#05070b" />
        </linearGradient>
        <linearGradient id="glow" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#74c0fc" stop-opacity="0.2" />
            <stop offset="100%" stop-color="#d6ad60" stop-opacity="0.08" />
        </linearGradient>
    </defs>
    <rect width="640" height="920" fill="url(#bg)" />
    <rect x="0" y="0" width="640" height="920" fill="url(#glow)" />
    <rect x="36" y="36" width="568" height="848" rx="26" fill="none" stroke="#74c0fc" stroke-opacity="0.22" stroke-width="3" />
    <path d="M86 196 H554" stroke="#d6ad60" stroke-opacity="0.34" stroke-width="2" />
    <path d="M86 736 H554" stroke="#4f9d8f" stroke-opacity="0.24" stroke-width="2" />
    <text x="86" y="146" fill="#9ecbff" font-family="Segoe UI, Arial, sans-serif" font-size="30" letter-spacing="6">JAQUETTE AUTO</text>
    <text x="86" y="320" fill="#f8fbff" font-family="Segoe UI, Arial, sans-serif" font-size="54" font-weight="700">$texteTitre</text>
    <text x="86" y="386" fill="#a7bac8" font-family="Segoe UI, Arial, sans-serif" font-size="24">$texteAuteur</text>
    <text x="86" y="790" fill="#d6ad60" font-family="Segoe UI, Arial, sans-serif" font-size="22" letter-spacing="4">FALLBACK LOCAL</text>
</svg>
SVG;

    return 'data:image/svg+xml;charset=utf-8,' . rawurlencode($svg);
}

function jaquetteLivre($titre, $couverture = '', $auteur = '')
{
    if ($couverture !== '') {
        return str_replace('../uploads/', 'uploads/', $couverture);
    }

    $jaquettes = [
        'Blame!' => 'https://cdn.myanimelist.net/images/manga/1/174389.jpg',
        'Berserk' => 'https://cdn.myanimelist.net/images/manga/1/157897.jpg',
        'Vagabond' => 'https://cdn.myanimelist.net/images/manga/1/259070.jpg',
        'Monster' => 'https://cdn.myanimelist.net/images/manga/3/258224.jpg',
        '20th Century Boys' => 'https://cdn.myanimelist.net/images/manga/5/260006.jpg',
        'Pluto' => 'https://cdn.myanimelist.net/images/manga/1/264496.jpg',
        'Goodnight Punpun' => 'https://cdn.myanimelist.net/images/manga/3/266834.jpg',
        'Solanin' => 'https://cdn.myanimelist.net/images/manga/2/179699.jpg',
        'Dead Dead Demon\'s Dededede Destruction' => 'https://cdn.myanimelist.net/images/manga/3/142079.jpg',
        'Vinland Saga' => 'https://cdn.myanimelist.net/images/manga/2/188925.jpg',
        'Kingdom' => 'https://cdn.myanimelist.net/images/manga/2/171872.jpg',
        'Dorohedoro' => 'https://cdn.myanimelist.net/images/manga/3/258246.jpg',
        'Gantz' => 'https://cdn.myanimelist.net/images/manga/1/278020.jpg',
        'Akira' => 'https://cdn.myanimelist.net/images/manga/3/271629.jpg',
        'Ghost in the Shell' => 'https://cdn.myanimelist.net/images/manga/2/155733.jpg',
        'Eden: It\'s an Endless World!' => 'https://cdn.myanimelist.net/images/manga/2/63117.jpg',
        'Biomega' => 'https://cdn.myanimelist.net/images/manga/2/211783.jpg',
        'Noise' => 'https://cdn.myanimelist.net/images/manga/1/177551.jpg',
        'Knights of Sidonia' => 'https://cdn.myanimelist.net/images/manga/2/286578.jpg',
        'Homunculus' => 'https://cdn.myanimelist.net/images/manga/1/318.jpg',
        'Ichi the Killer' => 'https://cdn.myanimelist.net/images/manga/3/174206.jpg',
        'Lone Wolf and Cub' => 'https://cdn.myanimelist.net/images/manga/1/159264.jpg',
        'The Climber' => 'https://cdn.myanimelist.net/images/manga/1/324690.jpg',
        'Innocent' => 'https://cdn.myanimelist.net/images/manga/1/222193.jpg',
        'Real' => 'https://cdn.myanimelist.net/images/manga/2/115969.jpg',
        'Planetes' => 'https://cdn.myanimelist.net/images/manga/3/170572.jpg',
        'The Fable' => 'https://cdn.myanimelist.net/images/manga/2/152911.jpg',
        'Ajin' => 'https://cdn.myanimelist.net/images/manga/3/307700.jpg',
        'Parasyte' => 'https://cdn.myanimelist.net/images/manga/2/188928.jpg',
        'Hellsing' => 'https://cdn.myanimelist.net/images/manga/3/13088.jpg',
        'Black Lagoon' => 'https://cdn.myanimelist.net/images/manga/1/227037.jpg',
        'Tokyo Ghoul' => 'https://cdn.myanimelist.net/images/manga/3/194456.jpg',
        'Bokurano' => 'https://cdn.myanimelist.net/images/manga/1/57107.jpg',
        'Devilman' => 'https://cdn.myanimelist.net/images/manga/2/1431.jpg',
        'Girls\' Last Tour' => 'https://cdn.myanimelist.net/images/manga/1/185918.jpg'
    ];

    if (isset($jaquettes[$titre])) {
        return $jaquettes[$titre];
    }

    return jaquetteFallbackLocale($titre, $auteur);
}

function jaquetteLivreAutomatique($titre, $couverture = '', $auteur = '')
{
    if ($couverture !== '') {
        return str_replace('../uploads/', 'uploads/', $couverture);
    }

    $jaquettes = [
        'Blame!' => 'https://cdn.myanimelist.net/images/manga/1/174389.jpg',
        'Berserk' => 'https://cdn.myanimelist.net/images/manga/1/157897.jpg',
        'Vagabond' => 'https://cdn.myanimelist.net/images/manga/1/259070.jpg',
        'Monster' => 'https://cdn.myanimelist.net/images/manga/3/258224.jpg',
        '20th Century Boys' => 'https://cdn.myanimelist.net/images/manga/5/260006.jpg',
        'Pluto' => 'https://cdn.myanimelist.net/images/manga/1/264496.jpg',
        'Goodnight Punpun' => 'https://cdn.myanimelist.net/images/manga/3/266834.jpg',
        'Solanin' => 'https://cdn.myanimelist.net/images/manga/2/179699.jpg',
        'Dead Dead Demon\'s Dededede Destruction' => 'https://cdn.myanimelist.net/images/manga/3/142079.jpg',
        'Vinland Saga' => 'https://cdn.myanimelist.net/images/manga/2/188925.jpg',
        'Kingdom' => 'https://cdn.myanimelist.net/images/manga/2/171872.jpg',
        'Dorohedoro' => 'https://cdn.myanimelist.net/images/manga/3/258246.jpg',
        'Gantz' => 'https://cdn.myanimelist.net/images/manga/1/278020.jpg',
        'Akira' => 'https://cdn.myanimelist.net/images/manga/3/271629.jpg',
        'Ghost in the Shell' => 'https://cdn.myanimelist.net/images/manga/2/155733.jpg',
        'Eden: It\'s an Endless World!' => 'https://cdn.myanimelist.net/images/manga/2/63117.jpg',
        'Biomega' => 'https://cdn.myanimelist.net/images/manga/2/211783.jpg',
        'Noise' => 'https://cdn.myanimelist.net/images/manga/1/177551.jpg',
        'Knights of Sidonia' => 'https://cdn.myanimelist.net/images/manga/2/286578.jpg',
        'Homunculus' => 'https://cdn.myanimelist.net/images/manga/1/318.jpg',
        'Ichi the Killer' => 'https://cdn.myanimelist.net/images/manga/3/174206.jpg',
        'Lone Wolf and Cub' => 'https://cdn.myanimelist.net/images/manga/1/159264.jpg',
        'The Climber' => 'https://cdn.myanimelist.net/images/manga/1/324690.jpg',
        'Innocent' => 'https://cdn.myanimelist.net/images/manga/1/222193.jpg',
        'Real' => 'https://cdn.myanimelist.net/images/manga/2/115969.jpg',
        'Planetes' => 'https://cdn.myanimelist.net/images/manga/3/170572.jpg',
        'The Fable' => 'https://cdn.myanimelist.net/images/manga/2/152911.jpg',
        'Ajin' => 'https://cdn.myanimelist.net/images/manga/3/307700.jpg',
        'Parasyte' => 'https://cdn.myanimelist.net/images/manga/2/188928.jpg',
        'Hellsing' => 'https://cdn.myanimelist.net/images/manga/3/13088.jpg',
        'Black Lagoon' => 'https://cdn.myanimelist.net/images/manga/1/227037.jpg',
        'Tokyo Ghoul' => 'https://cdn.myanimelist.net/images/manga/3/194456.jpg',
        'Bokurano' => 'https://cdn.myanimelist.net/images/manga/1/57107.jpg',
        'Devilman' => 'https://cdn.myanimelist.net/images/manga/2/1431.jpg',
        'Girls\' Last Tour' => 'https://cdn.myanimelist.net/images/manga/1/185918.jpg'
    ];

    if (isset($jaquettes[$titre])) {
        return $jaquettes[$titre];
    }

    $jaquette = jaquetteOpenLibrary($titre, $auteur);

    if ($jaquette !== '') {
        return $jaquette;
    }

    $jaquette = jaquetteGoogleBooks($titre, $auteur);

    if ($jaquette !== '') {
        return $jaquette;
    }

    $jaquette = jaquetteJikan($titre);

    if ($jaquette !== '') {
        return $jaquette;
    }

    return jaquetteFallbackLocale($titre, $auteur);
}

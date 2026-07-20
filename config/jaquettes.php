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

    $jaquette = jaquetteOpenLibrary($titre, $auteur);

    if ($jaquette !== '') {
        return $jaquette;
    }

    $jaquette = jaquetteGoogleBooks($titre, $auteur);

    if ($jaquette !== '') {
        return $jaquette;
    }

    return jaquetteJikan($titre);
}

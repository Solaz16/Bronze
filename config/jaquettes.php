<?php

function jaquetteLivre($titre, $couverture = '')
{
    if ($couverture !== '') {
        return $couverture;
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

    return $jaquettes[$titre] ?? '';
}

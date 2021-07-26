<?php

include_once __DIR__ . '/vendor/autoload.php';

$output = [];
$data = json_decode(file_get_contents(__DIR__ . '/pokemon-full-list.json'));

const END_OF_1ST_GENERATION = 152;
const END_OF_3RD_GENERATION = 386;

foreach ($data as $pokemon) {
    if ($pokemon->id >= END_OF_1ST_GENERATION) {
        break;
    }

    $id_pokedex = (string)$pokemon->id;
    while (strlen($id_pokedex) < 3) {
        $id_pokedex = "0$id_pokedex";
    }

    array_push(
        $output,
        [
            'id' => $pokemon->id,
            'name' => empty($pokemon->name->french)
                ? $pokemon->name->english
                : $pokemon->name->french,
            'image' => "https://raw.githubusercontent.com/fanzeyi/pokemon.json/master/thumbnails/{$id_pokedex}.png",
            'sprite' => "https://raw.githubusercontent.com/fanzeyi/pokemon.json/master/sprites/{$id_pokedex}MS.png",
        ]
    );
}

file_put_contents(__DIR__ . '/config/pokemons.json', json_encode($output));

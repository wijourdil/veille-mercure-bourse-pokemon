<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\PokemonController;
use Faker\Factory;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublishTrade extends Command
{
    protected $signature = 'trade:publish';

    protected $description = 'Publish a fake Pokemon trade';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        (new PokemonController())->proposeToTrade(
            new Request([
                'trainer_id' => Str::random(),
                'pokemon_id' => collect(json_decode(file_get_contents(config_path('pokemons.json'))))->random()->id,
                'do' => Factory::create()->firstName,
            ])
        );
        return 0;
    }
}

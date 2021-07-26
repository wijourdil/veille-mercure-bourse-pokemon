<?php

namespace App\Http\Controllers\Api;

use Anik\Mercure\Factory\Publisher;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mercure\Update;

class PokemonController extends Controller
{
    private Collection $pokemons;

    public function __construct()
    {
        $this->pokemons = collect(json_decode(file_get_contents(config_path('pokemons.json'))));
    }

    public function random(): JsonResponse
    {
        return new JsonResponse($this->pokemons->random());
    }

    public function all(): JsonResponse
    {
        return new JsonResponse($this->pokemons->toArray());
    }

    public function proposeToTrade(Request $request): JsonResponse
    {
        DB::table('pokemon_trades')->insert([
            'pokemon_id' => $request->pokemon_id,
            'trainer_id' => $request->trainer_id,
            'do' => $request->do,
        ]);

        if (DB::table('pokemon_trades')->count() >= 2) {
            $publisher = Publisher::instance([
                "url" => "https://localhost/.well-known/mercure",
                "jwt" => "eyJhbGciOiJIUzI1NiJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdLCJzdWJzY3JpYmUiOlsiaHR0cHM6Ly9leGFtcGxlLmNvbS9teS1wcml2YXRlLXRvcGljIiwie3NjaGVtZX06Ly97K2hvc3R9L2RlbW8vYm9va3Mve2lkfS5qc29ubGQiLCIvLndlbGwta25vd24vbWVyY3VyZS9zdWJzY3JpcHRpb25zey90b3BpY317L3N1YnNjcmliZXJ9Il0sInBheWxvYWQiOnsidXNlciI6Imh0dHBzOi8vZXhhbXBsZS5jb20vdXNlcnMvZHVuZ2xhcyIsInJlbW90ZUFkZHIiOiIxMjcuMC4wLjEifX19.z5YrkHwtkz3O_nOnhC_FP7_bmeISe3eykAkGbAl5K7c",
                "http_client" => HttpClient::create([
                    "verify_peer" => false,
                    "verify_host" => false,
                ]),
            ]);

            $tradeRow1 = DB::table('pokemon_trades')->inRandomOrder()->first();
            $tradeRow2 = DB::table('pokemon_trades')->where('id', '!=', $tradeRow1->id)->inRandomOrder()->first();

            $pokemonToTrade1 = $this->pokemons->firstWhere('id', '=', $tradeRow1->pokemon_id);
            $pokemonToTrade2 = $this->pokemons->firstWhere('id', '=', $tradeRow2->pokemon_id);

            $publisher(new Update(
                ["https://example.com/pokemon/trade/{$tradeRow1->trainer_id}"],
                json_encode([
                    'id' => $pokemonToTrade2->id,
                    'name' => $pokemonToTrade2->name,
                    'image' => $pokemonToTrade2->image,
                    'do' => $tradeRow2->do,
                ]),
            ));
            $publisher(new Update(
                ["https://example.com/pokemon/trade/{$tradeRow2->trainer_id}"],
                json_encode([
                    'id' => $pokemonToTrade1->id,
                    'name' => $pokemonToTrade1->name,
                    'image' => $pokemonToTrade1->image,
                    'do' => $tradeRow1->do,
                ]),
            ));

            DB::table('pokemon_trades')
                ->whereIn('id', [$tradeRow1->id, $tradeRow2->id])
                ->delete();
        }

        return new JsonResponse();
    }

    public function cancelTrade(string $trainer_id, string $pokemon_id): JsonResponse
    {
        DB::table('pokemon_trades')
            ->where('pokemon_id', '=', $pokemon_id)
            ->where('trainer_id', '=', $trainer_id)
            ->delete();

        return new JsonResponse();
    }
}

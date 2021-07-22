<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class PokemonController extends Controller
{
    public function random(): JsonResponse
    {
        return new JsonResponse([
            'name' => 'Pikachu',
        ]);
    }
}

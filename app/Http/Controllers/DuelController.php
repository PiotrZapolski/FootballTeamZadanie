<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class DuelController extends Controller
{
    public function startDuel(): JsonResponse
    {
        return response()->json();
    }

    public function getCurrentDuelData(): array
    {
        return [
            'round' => 4,
            'your_points' => 260,
            'opponent_points' => 100,
            'status' => 'active',
            'cards' => config('game.cards'),
        ];
    }

    public function selectCard(): JsonResponse
    {
        return response()->json();
    }

    public function getDuelsHistory(): array
    {
        return [
            [
                "id" => 1,
                "player_name" => "Jan Kowalski",
                "opponent_name" => "Piotr Nowak",
                "won" => 0
            ],
            [
                "id" => 2,
                "player_name" => "Jan Kowalski",
                "opponent_name" => "Tomasz Kaczyński",
                "won" => 1
            ],
            [
                "id" => 3,
                "player_name" => "Jan Kowalski",
                "opponent_name" => "Agnieszka Tomczak",
                "won" => 1
            ],
            [
                "id" => 4,
                "player_name" => "Jan Kowalski",
                "opponent_name" => "Michał Bladowski",
                "won" => 1
            ],
        ];
    }
}

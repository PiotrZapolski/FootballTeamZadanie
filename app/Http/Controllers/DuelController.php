<?php

namespace App\Http\Controllers;

use App\Http\Resources\DuelResource;
use App\Services\GameLogicService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class DuelController extends Controller
{
    public function __construct(private readonly GameLogicService $gameLogicService)
    {
    }

    /**
     * @return JsonResponse
     */
    public function startDuel(): JsonResponse
    {
        $this->gameLogicService->startDuel();

        return response()->json(['info' => 'OK']);
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

    /**
     * @return JsonResource
     */
    public function getDuelsHistory(): JsonResource
    {
        $user = auth()->user();

        $duels = $user->duels()
            ->finished()
            ->with(['rounds', 'fakeOpponent'])
            ->get();

        return DuelResource::collection($duels);
    }
}

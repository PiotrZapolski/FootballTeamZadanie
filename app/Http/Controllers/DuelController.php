<?php

namespace App\Http\Controllers;

use App\Http\Requests\SelectCardRequest;
use App\Http\Resources\ActiveDuelResource;
use App\Http\Resources\DuelResource;
use App\Models\Card;
use App\Services\GameLogicService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

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

    /**
     * @return JsonResource
     */
    public function nextRound(): JsonResource
    {
        $user = auth('sanctum')->user();
        $currentDuel = $user->getLastDuel();

        if ($currentDuel === null) {
            throw new UnprocessableEntityHttpException('You dont have active duel');
        }

        $this->gameLogicService->finishRound($currentDuel);

        if ($currentDuel->isFinished() === false) {
            $this->gameLogicService->newRound($currentDuel);
        }

        return new ActiveDuelResource($currentDuel);
    }

    /**
     * @param SelectCardRequest $request
     * @return JsonResponse
     */
    public function selectCard(SelectCardRequest $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        $card = Card::findByPivotId($request->validated('id'));
        $duel = $user->getLastDuel();

        if ($duel === null) {
            throw new UnprocessableEntityHttpException('You dont have any duels');
        }
        if ($card === null || $user->getAvailableCards()->contains($card) === false) {
            throw new AccessDeniedHttpException('You cant use this card.');
        }

        $this->gameLogicService->selectCard($duel, $card);

        return response()->json(['info' => 'OK']);
    }

    /**
     * @return JsonResource
     */
    public function getDuelsHistory(): JsonResource
    {
        $user = auth('sanctum')->user();

        $duels = $user->duels()
            ->finished()
            ->with(['rounds', 'fakeOpponent', 'user'])
            ->latest()
            ->get();

        return DuelResource::collection($duels);
    }
}

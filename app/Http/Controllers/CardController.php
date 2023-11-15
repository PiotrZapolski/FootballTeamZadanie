<?php

namespace App\Http\Controllers;

use App\Http\Resources\CardResource;
use App\Services\GameLogicService;

class CardController extends Controller
{
    public function __construct(private readonly GameLogicService $gameLogicService)
    {
    }

    /**
     * @return CardResource
     * @throws \Exception
     */
    public function getCard(): CardResource
    {
        $card = $this->gameLogicService->getNewCard();

        return new CardResource($card);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\CardResource;
use App\Services\CardsService;

class CardController extends Controller
{
    public function __construct(private readonly CardsService $cardsService)
    {
    }

    /**
     * @return CardResource
     */
    public function getCard(): CardResource
    {
        $card = $this->cardsService->getNewCard();

        return new CardResource($card);
    }
}

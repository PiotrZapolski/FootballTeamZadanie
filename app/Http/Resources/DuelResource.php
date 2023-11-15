<?php

namespace App\Http\Resources;

use App\Models\Duel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Duel
 */
class DuelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'player_name' => $this->user->username,
            'opponent_name' => $this->fakeOpponent->username,
            'won' => $this->isWon(),
        ];
    }
}

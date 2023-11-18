<?php

namespace App\Http\Resources;

use App\Models\Duel;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Duel
 */
class ActiveDuelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'round' => ($this->rounds->max('number') ?? 0) + 1,
            'your_points' => $this->user_points,
            'opponent_points' => $this->opponent_points,
            'status' => $this->status,
            'cards' => CardResource::collection($this->user->cards),
        ];
    }
}

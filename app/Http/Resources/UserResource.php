<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'level' => $this->level->number,
            'level_points' => $this->points,
            'cards' => CardResource::collection($this->cards),
            'new_card_allowed' => $this->isNewCardAllowed(),
        ];
    }
}

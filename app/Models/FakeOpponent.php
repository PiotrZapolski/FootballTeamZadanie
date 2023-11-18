<?php

namespace App\Models;

use App\Traits\PlayerAssetsTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string $username
 * @property Level $level
 * @property Duel $duel
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection $cards
 */
class FakeOpponent extends Model
{
    use HasFactory, PlayerAssetsTrait;

    protected $fillable = [
        'username', 'level_id',
    ];

    /**
     * @return HasMany
     */
    public function duel(): HasOne
    {
        return $this->hasOne(Duel::class, 'fake_opponent_id');
    }

    /**
     * @return Collection
     */
    public function getAvailableCards(): Collection
    {
        $usedCardIds = $this->duel->rounds->pluck('opponent_card_id');

        return $this->cards->reject(function ($card) use ($usedCardIds) {
            return $usedCardIds->contains($card->id);
        });
    }
}

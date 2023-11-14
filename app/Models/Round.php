<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $duel_id
 * @property int $number
 * @property int $user_card_id
 * @property int $opponent_card_id
 * @property int $user_points
 * @property int $opponent_points
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Duel $duel
 * @property-read Card $userCard
 * @property-read Card $opponentCard
 */
class Round extends Model
{
    use HasFactory;
    protected $fillable = [
        'duel_id',
        'number',
        'user_card_id',
        'opponent_card_id',
        'user_points',
        'opponent_points',
    ];

    /**
     * @return BelongsTo
     */
    public function duel(): BelongsTo
    {
        return $this->belongsTo(Duel::class);
    }

    /**
     * @return BelongsTo
     */
    public function userCard(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'user_card_id');
    }

    /**
     * @return BelongsTo
     */
    public function opponentCard(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'opponent_card_id');
    }

}

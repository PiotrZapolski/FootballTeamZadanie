<?php

namespace App\Models;

use App\Enums\DuelStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property int $fake_opponent_id
 * @property int $user_points
 * @property int $opponent_points
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @property-read FakeOpponent $fakeOpponent
 * @property-read Collection $rounds
 */
class Duel extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'fake_opponent_id', 'user_points', 'opponent_points', 'status'];

    protected $casts = [
        'user_points' => 'integer',
        'opponent_points' => 'integer',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function fakeOpponent(): BelongsTo
    {
        return $this->belongsTo(FakeOpponent::class);
    }

    /**
     * @return HasMany
     */
    public function rounds(): HasMany
    {
        return $this->hasMany(Round::class);
    }

    /**
    * @param Builder $query
    * @return Builder
    */
    public function scopeFinished(Builder $query): Builder
    {
        return $query->where('status', DuelStatusEnum::Finished->value);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', DuelStatusEnum::Active->value);
    }

    /**
     * @return bool
     */
    public function isWon(): bool
    {
        $userPoints = $this->rounds()->sum('user_points');
        $opponentPoints = $this->rounds()->sum('opponent_points');

        return $userPoints > $opponentPoints;
    }
}

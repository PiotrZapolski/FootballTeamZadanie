<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\PlayerAssetsTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $username
 * @property int $level_id
 * @property Level $level
 * @property int $level_points
 * @property Collection $cards
 * @property bool $new_card_allowed
 * @property string|null $email_verified_at
 * @property string $password
 * @property string $points
 * @property string|null $remember_token
 * @property Collection $duels
 * @property int $cards_count
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, PlayerAssetsTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'username',
        'level_id',
        'level_points',
        'cards',
        'new_card_allowed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * @return HasMany
     */
    public function duels(): HasMany
    {
        return $this->hasMany(Duel::class, 'user_id');
    }

    /**
     * @return string
     */
    public function getPointsAttribute(): string
    {
        if ($this->level->level_up_threshold === null) {
            return $this->level_points;
        }

        $currentPoints = $this->level_points;
        $neededPoints = $this->level->level_up_threshold;

        return $currentPoints . "/" . $neededPoints;
    }

    /**
     * @return int
     */
    public function getCardsCount(): int
    {
        return $this->cards()->count();
    }

    /**
     * @return bool
     */
    public function isNewCardAllowed(): bool
    {
        return $this->level->cards_limit > $this->getCardsCount();
    }
}

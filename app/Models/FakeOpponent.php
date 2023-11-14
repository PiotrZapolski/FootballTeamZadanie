<?php

namespace App\Models;

use App\Traits\PlayerAssetsTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $username
 * @property Level $level
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
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $number
 * @property int $cards_limit
 * @property int $level_up_threshold
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Level extends Model
{
    use HasFactory;
    protected $fillable = [
        'number',
        'cards_limit',
        'level_up_threshold',
    ];
}

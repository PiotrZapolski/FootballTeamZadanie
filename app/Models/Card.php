<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;


/**
 * @property int $id
 * @property string $name
 * @property int $power
 * @property string $image
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Pivot $pivot
 */
class Card extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'name', 'power', 'image'];

    public static function findByPivotId($pivotId)
    {
        return self::join('userable_cards', 'cards.id', '=', 'userable_cards.card_id')
            ->where('userable_cards.id', $pivotId)
            ->select('cards.*')
            ->first();
    }

}

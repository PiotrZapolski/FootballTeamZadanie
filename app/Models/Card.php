<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @property int $id
 * @property string $name
 * @property int $power
 * @property string $image
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Card extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'name', 'power', 'image'];

}

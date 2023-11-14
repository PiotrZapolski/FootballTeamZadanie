<?php

namespace App\Models;

use App\Traits\PlayerAssetsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FakeOpponent extends Model
{
    use HasFactory, PlayerAssetsTrait;

    protected $fillable = [
        'username', 'level',
    ];
}

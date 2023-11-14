<?php

namespace App\Traits;

use App\Models\Card;
use App\Models\Level;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait PlayerAssetsTrait
{
    /**
     * @return MorphToMany
     */
    public function cards(): MorphToMany
    {
        return $this->morphToMany(Card::class, 'userable', 'userable_cards');
    }

    /**
     * @return BelongsTo
     */
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }
}

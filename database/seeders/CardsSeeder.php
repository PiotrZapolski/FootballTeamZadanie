<?php

namespace Database\Seeders;

use App\Models\Card;
use Illuminate\Database\Seeder;

class CardsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //provided cards directly from config
        $cards = config('game.cards');

        foreach ($cards as $card) {
            Card::firstOrCreate($card);
        }
    }
}

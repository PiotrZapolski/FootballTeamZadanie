<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $levels = [
            ['number' => 1, 'cards_limit' => 5, 'level_up_threshold' => 100],
            ['number' => 2, 'cards_limit' => 10, 'level_up_threshold' => 160],
            ['number' => 3, 'cards_limit' => 15, 'level_up_threshold' => null],
        ];

        foreach ($levels as $level) {
            Level::firstOrCreate($level);
        }
    }
}

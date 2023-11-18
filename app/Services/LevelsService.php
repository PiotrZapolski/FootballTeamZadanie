<?php

namespace App\Services;

use App\Models\Level;
use App\Models\User;

class LevelsService
{
    private int $pointsForWin;
    public function __construct()
    {
        $this->pointsForWin = config('game.rules.level_points_for_win');
    }

    /**
     * @param User $user
     * @return void
     */
    public function addLevelPointsForWin(User $user): void
    {
        $level = $user->level;
        $user->level_points += intval($this->pointsForWin);

        if ($user->level_points >= $level->level_up_threshold && $level->level_up_threshold != null) {
            $this->levelUp($user);
        }

        $user->save();
    }

    /**
     * @param User $user
     * @return void
     */
    private function levelUp(User $user): void
    {
        $level = $user->level;
        $nextLevel = $this->getNextLevel($user);

        if ($nextLevel !== null) {
            $user->level_points = max(0, $user->level_points - $level->level_up_threshold);
            $user->level_id = $nextLevel->id;
        }
    }

    /**
     * @param User $user
     * @return Level|null
     */
    private function getNextLevel(User $user): ?Level
    {
        $currentLevel = $user->level;
        return Level::where('number', '>', $currentLevel->number)->orderBy('number')->first();
    }
}

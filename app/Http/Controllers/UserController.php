<?php

namespace App\Http\Controllers;

class UserController extends Controller
{
    public function getUserData(): array
    {
        return [
            'id' => 1,
            'username' => 'Test User',
            'level' => 1,
            'level_points' => '40/100',
            'cards' => config('game.cards'),
            'new_card_allowed' => true,
        ];
    }
}

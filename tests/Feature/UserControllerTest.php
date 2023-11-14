<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CardsSeeder;
use Database\Seeders\LevelsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LevelsSeeder::class);
        $this->seed(CardsSeeder::class);
    }

    public function testGetUserDataWithNewCardAllowed()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/user-data');

        $response->assertOk();

        $response->assertJson([
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
                'level' => $user->level->number,
                'level_points' => $user->getPointsAttribute(),
                'cards' => [],
                'new_card_allowed' => true,
            ],
        ]);
    }

    public function testGetUserDataWithNewCardNotAllowed()
    {
        $user = User::factory()->create();
        $user->cards()->attach(range(1, 5));

        $response = $this->actingAs($user)->getJson('/api/user-data');

        $response->assertOk();

        $response->assertJson([
            'data' => [
                'id' => $user->id,
                'username' => 'Test User',
                'level' => 1,
                'level_points' => '40/100',
                'cards' => [
                    ['id' => 1, 'name' => 'Sergio Donputamadre', 'power' => 101, 'image' => 'card-1.jpg'],
                    ['id' => 2, 'name' => 'Lewan RS', 'power' => 69, 'image' => 'card-2.jpg'],
                    ['id' => 3, 'name' => 'Enpi12', 'power' => 85, 'image' => 'card-3.jpg'],
                    ['id' => 4, 'name' => 'Drivery', 'power' => 61, 'image' => 'card-4.jpg'],
                    ['id' => 5, 'name' => 'Maximus', 'power' => 18, 'image' => 'card-5.jpg'],
                ],
                'new_card_allowed' => false,
            ],
        ]);
    }
}

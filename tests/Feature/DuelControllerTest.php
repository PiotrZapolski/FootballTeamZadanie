<?php

namespace Tests\Feature;

use App\Enums\DuelStatusEnum;
use App\Models\Duel;
use App\Models\User;
use Database\Seeders\CardsSeeder;
use Database\Seeders\LevelsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DuelControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LevelsSeeder::class);
        $this->seed(CardsSeeder::class);
    }

    public function testGetUserGetDuelsHistorySuccess()
    {
        $user = User::factory()->create();

        $duels = Duel::factory(3)->create([
            'user_id' => $user->id,
            'status' => DuelStatusEnum::Finished->value,
        ]);

        $this->actingAs($user);

        $response = $this->getJson('/api/duels');

        $response->assertOk();
        $response->assertJsonCount(3);

        /** @var Duel $duel */
        foreach ($duels as $duel) {
            $response->assertJsonFragment([
                'id' => $duel->id,
                'player_name' => $duel->user->username,
                'opponent_name' => $duel->fakeOpponent->username,
                'won' => $duel->isWon(),
            ]);
        }

        $this->assertEquals(3, Duel::count());
    }

    public function testGetUserGetDuelsHistoryWithOnlyFishedOnesSuccess()
    {
        $user = User::factory()->create();

        // 5 finished duels
        Duel::factory(5)->create([
            'user_id' => $user->id,
            'status' => DuelStatusEnum::Finished->value,
        ]);

        // 1 active duel
        Duel::factory()->create([
            'user_id' => $user->id,
            'status' => DuelStatusEnum::Active->value,
        ]);

        $this->actingAs($user);

        $response = $this->getJson('/api/duels');

        $response->assertOk();
        $response->assertJsonCount(5); // Only finished should be returned
        $response->assertJsonStructure([
            '*' => [
                'id',
                'player_name',
                'opponent_name',
                'won',
            ]
        ]);

        $this->assertEquals(6, Duel::count());
    }

    public function testUserStartsDuelSuccess()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/duels');

        $response->assertOk();
        $response->assertJson(['info' => 'OK']);

        $this->assertDatabaseHas('duels', [
            'user_id' => $user->id,
        ]);

        /** @var Duel $duel */
        $duel = Duel::latest()->first();
        $this->assertEquals($user->id, $duel->user_id);
        $this->assertEquals(0, $duel->user_points);
        $this->assertEquals(0, $duel->opponent_points);
        $this->assertEquals(
            DuelStatusEnum::Active->value,
            $duel->status
        );
        $this->assertNotNull($duel->fake_opponent_id);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CardsSeeder;
use Database\Seeders\LevelsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LevelsSeeder::class);
        $this->seed(CardsSeeder::class);

        config(['app.debug' => false]);
    }

    public function testUserGetNewCardSuccess()
    {
        $user = User::factory()->create();

        $this->assertSame(0, $user->cards()->count());

        $response = $this->actingAs($user)->postJson('/api/cards');

        $response->assertOk();


        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'power',
                'image',
            ],
        ]);

        $user->refresh();

        $this->assertSame(1, $user->cards()->count());
    }

    public function testUserGetNewCardFailure()
    {
        $user = User::factory()->create();
        $cardsLimit = $user->level->cards_limit;

        $user->cards()->attach(range(1, $cardsLimit));

        $this->assertEquals($cardsLimit, $user->cards()->count());

        $response = $this->actingAs($user)->postJson('/api/cards');

        $response->assertStatus(403);
        $response->assertExactJson(['message' => 'You have reached the limit of cards for your level']);

    }
}

<?php

namespace Tests\Feature;

use App\Enums\DuelStatusEnum;
use App\Models\Card;
use App\Models\Duel;
use App\Models\FakeOpponent;
use App\Models\Level;
use App\Models\Round;
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
                'won' => $duel->isWon() ? 1 : 0,
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
        $this->assertEquals($user->level_id, $duel->fakeOpponent->level_id);
        $this->assertEquals($user->level->cards_limit, $duel->fakeOpponent->cards()->count());
    }

    public function testFirstDuelActiveCallSuccess()
    {
        $user = User::factory()->create();
        $duel = Duel::factory()->create([
            'user_id' => $user->id,
            'status' => DuelStatusEnum::Active->value,
            'user_points' => 0,
            'opponent_points' => 0,
        ]);
        $user->cards()->attach(Card::first());
        $card = $user->cards->first();

        $this->actingAs($user);
        $response = $this->getJson('/api/duels/active');

        $response->assertOk();
        $response->assertJson([
            'round' => 1,
            'your_points' => 0,
            'opponent_points' => 0,
            'status' => DuelStatusEnum::Active->value,
            'cards' => [
                [
                    'id' => $card->pivot->id,
                    'name' => $card->name,
                    'power' => $card->power,
                    'image' => $card->image,
                ]
            ]
        ]);
        $this->assertCount(1, $duel->rounds);
        $round = $duel->rounds->first();
        $this->assertNull($round->user_card_id);
        $this->assertEquals(0, $round->user_points);
        $this->assertEquals(0, $round->opponent_points);
        $this->assertEquals(0, $round->opponent_card_id);
    }

    public function testUserySelectsCardSuccess()
    {
        $user = User::factory()->create();
        $fakeOpponent = FakeOpponent::factory()->create([
            'level_id' => $user->level_id
        ]);
        $fakeOpponent->cards()->attach(range(1, $user->level->cards_limit));
        $duel = Duel::factory()->create([
            'user_id' => $user->id,
            'fake_opponent_id' => $fakeOpponent->id,
            'status' => DuelStatusEnum::Active->value,
            'user_points' => 0,
            'opponent_points' => 0,
        ]);
        $round = Round::factory()->create([
            'duel_id' => $duel->id,
        ]);
        $user->cards()->attach(Card::first());
        $card = $user->cards->first();

        $this->actingAs($user);
        $response = $this->postJson('/api/duels/action', ['id' => $card->pivot->id]);

        $response->assertOk();
        $response->assertJson(['info' => 'OK']);
        $this->assertCount(1, $duel->rounds);
        $round->refresh();
        $this->assertEquals($round->user_card_id, $card->id);
        $this->assertEquals($round->user_points, $card->power);
        $this->assertEquals(0, $round->opponent_points);
        $this->assertNull($round->opponent_card_id);
    }

    public function testUserCannotSelectCardNotInPossessionFailure()
    {
        $user = User::factory()->create();
        $fakeOpponent = FakeOpponent::factory()->create([
            'level_id' => $user->level_id
        ]);
        $fakeOpponent->cards()->attach(range(1, $user->level->cards_limit));
        $duel = Duel::factory()->create([
            'user_id' => $user->id,
            'fake_opponent_id' => $fakeOpponent->id,
            'status' => DuelStatusEnum::Active->value,
            'user_points' => 0,
            'opponent_points' => 0,
        ]);
        $user->cards()->attach(Card::first());
        $card = $user->cards->first();
        $round = Round::factory()->create([
            'duel_id' => $duel->id,
            'user_card_id' => $card->id,
        ]);

        $this->actingAs($user);
        $response = $this->postJson('/api/duels/action', ['id' => $card->pivot->id]);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'You cant use this card.'
        ]);
    }

    public function testNextRoundStartsWithoutCardSelectedByUserSuccess()
    {
        $user = User::factory()->create();
        $fakeOpponent = FakeOpponent::factory()->create([
            'level_id' => $user->level_id
        ]);
        $fakeOpponent->cards()->attach(range(1, $user->level->cards_limit));
        $duel = Duel::factory()->create([
            'user_id' => $user->id,
            'fake_opponent_id' => $fakeOpponent->id,
            'status' => DuelStatusEnum::Active->value,
            'user_points' => 0,
            'opponent_points' => 0,
        ]);
        $round = Round::factory()->create([
            'duel_id' => $duel->id,
        ]);
        $user->cards()->attach(Card::first());
        $card = $user->cards->first();

        $this->actingAs($user);
        $response = $this->getJson('/api/duels/active');
        $duel->refresh();

        $response->assertOk();
        $response->assertJson([
            'round' => $round->number + 1,
            'your_points' => 0,
            'opponent_points' => $duel->opponent_points,
            'status' => DuelStatusEnum::Active->value,
            'cards' => [
                [
                    'id' => $card->pivot->id,
                    'name' => $card->name,
                    'power' => $card->power,
                    'image' => $card->image,
                ]
            ]
        ]);
        $this->assertCount(2, $duel->rounds);
        $round->refresh();
        $this->assertNull($round->user_card_id);
        $this->assertEquals($duel->user_points, $round->user_points);
        $this->assertEquals($duel->opponent_points, $round->opponent_points);
    }

    public function testDuelEndsAndUserGetsLevelPointsSuccess()
    {
        config(['game.rules.duel_max_rounds' => 1]);
        config(['game.rules.level_points_for_win' => 20]);
        $level = Level::first();
        $user = User::factory()->create([
            'level_id' => $level->id,
            'level_points' => $level->level_up_threshold - 20,
        ]);
        $fakeOpponent = FakeOpponent::factory()->create([
            'level_id' => $user->level_id
        ]);
        $fakeOpponent->cards()->attach(range(1, $user->level->cards_limit));
        $duel = Duel::factory()->create([
            'user_id' => $user->id,
            'fake_opponent_id' => $fakeOpponent->id,
            'status' => DuelStatusEnum::Active->value,
            'user_points' => 0,
            'opponent_points' => 0,
        ]);
        $round = Round::factory()->create([
            'number' => 1,
            'duel_id' => $duel->id,
            'user_points' => 1000,
            'opponent_card_id' => null,
        ]);
        $user->cards()->attach(Card::first());
        $card = $user->cards->first();

        $this->actingAs($user);
        $response = $this->getJson('/api/duels/active');
        $duel->refresh();

        $response->assertOk();
        $response->assertJson([
            'round' => 2,
            'your_points' => 1000,
            'opponent_points' => $duel->opponent_points,
            'status' => DuelStatusEnum::Finished->value,
            'cards' => [
                [
                    'id' => $card->pivot->id,
                    'name' => $card->name,
                    'power' => $card->power,
                    'image' => $card->image,
                ]
            ]
        ]);
        $this->assertCount(1, $duel->rounds);
        $user->refresh();
        $this->assertEquals(DuelStatusEnum::Finished->value, $duel->status);
        $this->assertEquals(0, $user->level_points);
        $this->assertEquals($level->number + 1, $user->level->number);
    }
}

<?php

namespace App\Services;

use App\Enums\DuelStatusEnum;
use App\Models\Card;
use App\Models\Duel;
use App\Models\FakeOpponent;
use App\Models\Level;
use Faker\Generator as Faker;
use Illuminate\Contracts\Auth\Authenticatable;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class GameLogicService
{
    public function __construct(private Faker $faker)
    {
    }

    /**
     * @return Card
     */
    public function getNewCard(): Card
    {
        $user = auth()->user();
        $user->load(['level', 'cards']);

        if ($user->isNewCardAllowed() === false) {
            throw new AccessDeniedHttpException('You have reached the limit of cards for your level');
        }

        /** @var Card $card */
        $card = Card::query()
            ->inRandomOrder()
            ->first();

        $user->cards()->attach($card->id);

        return $card;
    }

    /**
     * @return void
     */
    public function startDuel(): void
    {
        $user = auth()->user();

        $fakeOpponent = $this->createFakeOpponent($user->level);

        $this->createDuel($user, $fakeOpponent);
    }

    /**
     * @param Authenticatable $user
     * @param FakeOpponent $fakeOpponent
     * @return Duel
     */
    private function createDuel(Authenticatable $user, FakeOpponent $fakeOpponent): Duel
    {
        return Duel::create([
            'user_id' => $user->id,
            'fake_opponent_id' => $fakeOpponent->id,
            'status' => DuelStatusEnum::Active->value,
        ]);
    }

    /**
     * @param Level $level
     * @return FakeOpponent
     */
    private function createFakeOpponent(Level $level): FakeOpponent
    {
        $fakeOpponent = FakeOpponent::create([
            'level_id' => $level->id,
            'username' => $this->faker->userName,
        ]);

        $cardsLimit = $level->cards_limit;
        $cards = Card::inRandomOrder()->take($cardsLimit)->get();
        $fakeOpponent->cards()->attach($cards);

        return $fakeOpponent;
    }
}

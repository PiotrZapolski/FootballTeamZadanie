<?php

namespace App\Services;

use App\Enums\DuelStatusEnum;
use App\Models\Card;
use App\Models\Duel;
use App\Models\FakeOpponent;
use App\Models\Level;
use App\Models\Round;
use Faker\Generator as Faker;
use Illuminate\Contracts\Auth\Authenticatable;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class GameLogicService
{
    private int $roundsLimit;
    public function __construct
    (
        private readonly Faker         $faker,
        private readonly LevelsService $levelsService,
    )
    {
        $this->roundsLimit = config('game.rules.duel_max_rounds');
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

        return $user->cards()->where('cards.id', $card->id)->first();
    }

    /**
     * @return void
     */
    public function startDuel(): void
    {
        $user = auth()->user();

        if ($user->hasActiveDuel() === false) {
            $fakeOpponent = $this->createFakeOpponent($user->level);
            $this->createDuel($user, $fakeOpponent);
        }
    }

    /**
     * @param Duel $duel
     * @return Round
     */
    public function newRound(Duel $duel): Round
    {
        $lastRound = $duel->lastRound;

        /** @var Round $round */
        $round = $duel->rounds()->create([
            'number' => ($lastRound?->number ?? 0) + 1,
        ]);

        return $round;
    }

    /**
     * @param Duel $duel
     * @param Card $card
     * @return void
     */
    public function selectCard(Duel $duel, Card $card): void
    {
        $duel->currentRound->update([
            'user_card_id' => $card->id,
            'user_points' => $card->power,
        ]);
    }

    /**
     * @param Duel $duel
     * @return void
     */
    public function finishRound(Duel $duel): void
    {
        $round = $duel->currentRound;
        if ($round !== null) {
            $opponentCard = $this->drawOpponentsCard($duel->fakeOpponent);

            $round->update([
                'opponent_card_id' => $opponentCard->id,
                'opponent_points' => $opponentCard->power,
            ]);

            $this->updateDuelData($duel, $round);

            if ($round->number >= $this->roundsLimit) {
                $this->finishDuel($duel);
            }
        }
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

    /**
     * @param FakeOpponent $fakeOpponent
     * @return Card
     */
    private function drawOpponentsCard(FakeOpponent $fakeOpponent): Card
    {
        return $fakeOpponent->getAvailableCards()
            ->shuffle()
            ->first();
    }

    /**
     * @param Duel $duel
     * @param Round $round
     * @return void
     */
    private function updateDuelData(Duel $duel, Round $round): void
    {
        $duel->user_points += $round->user_points;
        $duel->opponent_points += $round->opponent_points;
        $duel->save();
    }

    /**
     * @param Duel $duel
     * @return void
     */
    private function finishDuel(Duel $duel): void
    {
        $duel->update([
            'status' => DuelStatusEnum::Finished->value,
        ]);

        if ($duel->user_points > $duel->opponent_points) {
            $this->levelsService->addLevelPointsForWin($duel->user);
        }
    }
}

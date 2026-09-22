<?php

namespace App\Traits\Providers;

use App\Helpers\Core as Helper;
use App\Models\Game;
use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

trait PrivateGamesTrait
{

    /**
     * @dev victormsalatiel - Corra de golpista, me chame no instagram
     * @param string $token
     * @param array $settingGame
     * @param array $iconData
     * @param array $activeLines
     * @param array $dropLine
     * @param array $betSizeList
     * @param array $multipleList
     * @param array $feature
     * @return JsonResponse
     */
    public static function SessionStructure(string $token, array $settingGame, array $iconData, array $activeLines, array $dropLine, array $betSizeList, array $multipleList, array $feature, array $featureResult = [])
    {
        $tokenOpen  = \Helper::DecToken($token);
        $setting    = \Helper::getSetting();

        if(isset($tokenOpen['status']) && $tokenOpen['status']) {
            $user = User::find($tokenOpen['id']);
            $totalBalance = 0;

            if ($user->is_demo_agent) {
                $totalBalance = $user->wallet->balance_demo;
            }else{
                $totalBalance = $user->wallet->total_balance;
            }

            $data                       = new \stdClass();
            $data->user_name            = $user->name;
            $data->credit               = $totalBalance;
            $data->num_line             = $settingGame['num_line'];
            $data->line_num             = $settingGame['line_num'];
            $data->bet_amount           = $settingGame['bet_amount'];
            $data->free_num             = $settingGame['free_num'];
            $data->free_total           = $settingGame['free_total'];
            $data->free_amount          = $settingGame['free_amount'];
            $data->free_multi           = $settingGame['free_multi'];
            $data->freespin_mode        = $settingGame['freespin_mode'];
            $data->multiple_list        = $multipleList;
            $data->credit_line          = $settingGame['credit_line'];
            $data->buy_feature          = $settingGame['buy_feature'];
            $data->buy_max              = $settingGame['buy_max'];
            $data->feature              = $feature;
            $data->total_way            = $settingGame['total_way'];
            $data->multiply             = $settingGame['multiply'];
            $data->icon_data            = $iconData;
            $data->active_lines         = $activeLines;
            $data->drop_line            = $dropLine;
            $data->currency_prefix      = $user->wallet->symbol;
            $data->currency_suffix      = "";
            $data->currency_thousand    = ".";
            $data->currency_decimal     = ",";
            $data->bet_size_list        = $betSizeList;

            $data->previous_session     = $settingGame['previous_session'];
            $data->game_state           = $settingGame['game_state'];
            $data->feature_result       = $featureResult;

            return response()->json([
                "data" => $data,
                "success" => true,
                "message" => "Session success"
            ]);
        }

        return response()->json([], 400);
    }


    /**
     * @dev victormsalatiel - Corra de golpista, me chame no instagram
     * @param string $token
     * @param array $settingGame
     * @param array $pull
     * @param array $dataLose
     * @param array $dataDemo
     * @param array $dataWin
     * @return JsonResponse
     */
    /**
     * The two welcome bonus rules that live outside the wallet: an expired bonus
     * is cleared, and while bonus money is in play the stake is capped.
     *
     * Returns null when the spin may go ahead, or the response to send back.
     */
    public static function enforceBonusRules($wallet, float $bet)
    {
        /// an expired bonus is no longer money
        if (!empty($wallet->bonus_expires_at) && now()->greaterThan($wallet->bonus_expires_at)) {
            if (floatval($wallet->balance_bonus) > 0 || floatval($wallet->balance_bonus_rollover) > 0) {
                $wallet->update([
                    'balance_bonus'          => 0,
                    'balance_bonus_rollover' => 0,
                    'bonus_expires_at'       => null,
                ]);
                $wallet->refresh();
            }
        }

        /// The stake cap used to refuse the spin with a 400. The games are
        /// third-party clients that cannot read our message, so every refusal
        /// surfaced as "Please Check Your Connection!" or, on some of them, a
        /// Portuguese "you have no balance" while the balance was plainly on
        /// screen. Nine of the twelve games looked broken for anyone holding a
        /// bonus who nudged the stake up.
        ///
        /// The published terms do not say the bet is refused either. They say
        /// the "maximum qualifying wager", which is the usual industry wording:
        /// stake what you like, but anything above the cap does not count
        /// towards clearing the bonus. That is what happens now, in
        /// betCountsTowardRollover() below.
        return null;
    }

    /**
     * Whether this stake counts towards clearing the bonus.
     *
     * A bet over the cap is allowed and is paid from the bonus like any other,
     * it simply does not bring the wagering requirement down, so the bonus
     * cannot be cleared in a handful of oversized spins.
     */
    /**
     * True when the request carries a sign-in for a different account than the
     * game token belongs to.
     *
     * A plain game request carries no sign-in at all, which is fine and returns
     * false. Only a request that proves it is somebody else is turned away.
     */
    protected static function spinnerIsSomebodyElse($tokenUser): bool
    {
        try {
            if (!request()->bearerToken()) {
                return false;
            }
            if (!auth('api')->check()) {
                return false;
            }
            return (int) auth('api')->id() !== (int) $tokenUser->id;
        } catch (\Throwable $e) {
            /// an unreadable or expired sign-in is not proof of anything
            return false;
        }
    }

    public static function betCountsTowardRollover($wallet, float $bet): bool
    {
        $setting = \Helper::getSetting();
        $maxBet  = (float) ($setting->bonus_max_bet ?? 0);

        if ($maxBet <= 0) {
            return true;
        }

        $playingWithBonus = floatval($wallet->balance_bonus) > 0
            || floatval($wallet->balance_bonus_rollover) > 0;

        if (!$playingWithBonus) {
            return true;
        }

        return $bet <= $maxBet;
    }

    /**
     * What one result row pays, as a multiple of (cpl * betamount).
     *
     * Most of the games put that number at index 5. Queen of Bounty and
     * Treasures of Aztec leave it empty and only describe the winning lines, and
     * before this those two paid nothing at all on a win.
     */
    public static function rowPayout(array $row): float
    {
        if (isset($row[5]) && is_numeric($row[5])) {
            return (float) $row[5];
        }

        $total = 0.0;
        foreach (($row[2] ?? []) as $line) {
            if (!is_array($line)) {
                continue;
            }
            $total += (float) ($line['payout'] ?? 0) * max(1.0, (float) ($line['multiply'] ?? 1));
        }

        return $total;
    }

    /**
     * Pick the spin result for a real money round.
     *
     * `$rtp` is the percentage of the stake the game should give back over time,
     * which is what the field in Admin > Games says it is. A winning row pays
     * payout / num_line times the bet, so the chance of landing on one is the
     * target divided by the average win. The cap keeps a game with tiny prizes
     * from winning on nearly every spin.
     */
    public static function drawResult(array $winResults, array $loseResults, int $rtp, int $numline, ?string $poolKey = null): array
    {
        $rtp     = max(0, min(100, $rtp));
        $numline = max(1, $numline);

        $averageWin = self::averageWin($winResults, $numline, $poolKey);

        $chance = 0.0;
        if ($averageWin > 0 && $rtp > 0) {
            $chance = min(0.9, ($rtp / 100) / $averageWin);
        }

        $wantsWin = !empty($winResults) && mt_rand(1, 1000000) <= (int) round($chance * 1000000);
        $pool     = $wantsWin ? $winResults : $loseResults;

        if (empty($pool)) {
            $pool = $wantsWin ? $loseResults : $winResults;
        }
        if (empty($pool)) {
            return [];
        }

        return $pool[array_rand($pool)];
    }

    /**
     * What an average winning row pays, per line.
     *
     * Two of the games carry a win table of several hundred rows, and working
     * this out from scratch on every spin cost about 20 ms of processor time
     * for each of them. The answer only changes when the game's own table
     * changes, so it is remembered against the game code and the size of the
     * table: a new table has a different size and so a different key, and any
     * deploy clears the cache anyway.
     */
    protected static function averageWin(array $winResults, int $numline, ?string $poolKey = null): float
    {
        if (empty($winResults)) {
            return 0.0;
        }

        $compute = function () use ($winResults, $numline): float {
            $total = 0.0;
            foreach ($winResults as $row) {
                $total += self::rowPayout($row);
            }
            return ($total / count($winResults)) / $numline;
        };

        if ($poolKey === null) {
            return $compute();
        }

        $key = 'spin:averagewin:' . $poolKey . ':' . count($winResults) . ':' . $numline;

        try {
            return (float) Cache::rememberForever($key, $compute);
        } catch (\Throwable $e) {
            /// a cache that cannot be written must never stop a spin
            return $compute();
        }
    }

    public static function SpinStructure(string $token, array $settingGame, array $pull, array $dataLose, array $dataDemo, array $dataWin, array $dataBonus)
    {
        $totalBalance = 0;
        $tokenOpen = \Helper::DecToken($token);

        if(isset($tokenOpen['status']) && $tokenOpen['status']) {
            $game               = Game::whereStatus(1)->where('game_code', $tokenOpen['game'])->first();
            $user               = User::find($tokenOpen['id']);
            $wallet             = Wallet::where('user_id', $tokenOpen['id'])->whereActive(1)->first();

            if(empty($game) || empty($user) || empty($wallet)) {
                return response()->json([], 400);
            }

            /// The game runs in an iframe and sends no sign-in header, so the
            /// signed token in its address is what says who is playing. That is
            /// how these game clients work and the route is open on purpose.
            ///
            /// But if a request does arrive signed in as somebody else, it is
            /// not a game: it is one account spending another account's money,
            /// which worked until now. Anyone who saw the game address, over a
            /// shoulder or in a shared screenshot, could empty that balance.
            if (self::spinnerIsSomebodyElse($user)) {
                return response()->json(['message' => 'This game session belongs to another account.'], 403);
            }

            $cpl                = intval($settingGame['cpl']);
            $amount             = floatval($settingGame['betamount']);
            $numline            = intval($settingGame['num_line']);
            $bet                = $amount * $cpl * $numline;
            $betInitial         = $bet;

            define("SLOTINCONS", 0);
            define("ACTIVEICONS", 1);
            define("ACTIVELINES", 2);
            define("DROPLINEDATA", 3);
            define("MULTIPLYCOUNT", 4);
            define("PAYOUT", 5);

            $loseResults        = $dataLose;
            $demoWinResults     = $dataDemo;

//            $checkFirstDeposit  = Transaction::where('user_id', auth()->id())->where('status', 1)->count();
//            if($checkFirstDeposit == 1 || $checkFirstDeposit == 2) {
//                $winResults     = $dataWin;
//            }else{
//                $winResults     = $bet >= 10 && $bet <= 50 ? $dataWin : [];
//            }

            $winResults         = $dataWin;
            $bonusResults       = $dataBonus;

            shuffle($loseResults);
            shuffle($demoWinResults);
            shuffle($winResults);
            shuffle($bonusResults);

            if ($user->is_demo_agent) {
                /// the demo account is meant to look good, it does not touch real money
                $winResults      = array_merge($winResults, $demoWinResults);
                $possibleResults = array_merge(
                    array_slice($winResults, 0, 90),
                    array_slice($loseResults, 0, 10)
                );
                if (empty($possibleResults)) {
                    $possibleResults = $dataLose ?: $dataWin;
                }
                shuffle($possibleResults);
                $result = $possibleResults[0];
            } else {
                /// The RTP set in Admin > Games is the share of the stake the game
                /// gives back over time. The prizes in the win pool are large
                /// (tens of times the stake), so the chance of a winning spin is
                /// the target divided by what an average win pays.
                $result = self::drawResult($winResults, $loseResults, intval($game->rtp), $numline, $game->game_code);
            }

            if (empty($result)) {
                return response()->json([], 400);
            }

            $changeBonus = 'balance';

            if ($user->is_demo_agent) {
                $wallet->decrement('balance_demo', $bet); /// retira do bonus
                $changeBonus = 'balance_demo'; /// define o tipo de transação
            }else{
                if ($bet <= 0) {
                    return response()->json("Insuficient balances", 400);
                }

                /// The bonus terms cap the stake while bonus money is in play and
                /// give the bonus a life span. Both were only ever written down,
                /// so a player could clear a 20x rollover in a handful of large
                /// spins, or months later.
                $refused = self::enforceBonusRules($wallet, $bet);
                if ($refused !== null) {
                    return $refused;
                }

                /// a stake over the cap still spins, it just does not count
                /// towards clearing the bonus
                $countsTowardRollover = self::betCountsTowardRollover($wallet, $bet);

                /// deduz o saldo apostado. The wallet is locked for the check and the
                /// deduction together, so two spins fired at once cannot both pass.
                $changeBonus = \DB::transaction(function () use ($wallet, $bet) {
                    $locked = Wallet::whereKey($wallet->id)->lockForUpdate()->first();
                    if (empty($locked) || floatval($locked->total_balance) < $bet) {
                        return null;
                    }

                    if(floatval($locked->balance_bonus) >= $bet) {
                        $locked->decrement('balance_bonus', $bet); /// retira do bonus
                        return 'balance_bonus';
                    }
                    if(floatval($locked->balance) >= $bet) {
                        $locked->decrement('balance', $bet); /// retira do saldo depositado
                        return 'balance';
                    }
                    if(floatval($locked->balance_withdrawal) >= $bet) {
                        $locked->decrement('balance_withdrawal', $bet); /// retira do saldo liberado pra saque
                        return 'balance_withdrawal';
                    }

                    /// the bet is spread over more than one balance, take it in order
                    $remaining = $bet;
                    $first     = null;
                    foreach (['balance_bonus', 'balance', 'balance_withdrawal'] as $column) {
                        $take = min($remaining, floatval($locked->{$column}));
                        if($take > 0) {
                            $locked->decrement($column, $take);
                            $first = $first ?? $column;
                            $remaining -= $take;
                        }
                        if($remaining <= 0) break;
                    }

                    return $first ?? 'balance';
                });

                if (empty($changeBonus)) {
                    return response()->json("Insuficient balances", 400);
                }

                $wallet->refresh();
            }

            /// registra a aposta no historico (orders), igual aos provedores externos
            $transactionId = 'src_' . $game->game_code . '_' . $user->id . '_' . Str::uuid()->toString();
            $order = null;
            if ($user->is_demo_agent == 0) {
                $order = Order::create([
                    'user_id'        => $user->id,
                    'session_id'     => $token,
                    'transaction_id' => $transactionId,
                    'type'           => 'bet',
                    'type_money'     => $changeBonus,
                    'amount'         => $bet,
                    'providers'      => 'source',
                    'game'           => $game->game_name,
                    'game_uuid'      => $game->game_code,
                    'round_id'       => 1,
                ]);

                $wallet->increment('total_bet', $bet);
            }

            $winAmount = $cpl * $amount * self::rowPayout($result); // valor do premio
            $result[ACTIVELINES][0]["win_amount"] = $winAmount;

            $pull['WinAmount']      = $winAmount;
            $pull['WinOnDrop']      = $winAmount;

            $pull['SlotIcons']      = $result[0];
            $pull['ActiveIcons']    = $result[1];
            $pull['ActiveLines']    = $result[2];
            $pull['DropLineData']   = $result[3];

            if ($user->is_demo_agent) {
                $totalBalance = $user->wallet->balance_demo;
            }else{
                $totalBalance = $user->wallet->total_balance;
            }

            $data = [
                "credit"            => $totalBalance,
                "freemode"          => $settingGame['freemode'] ?? false,
                "jackpot"           => $settingGame['jackpot'],
                "free_spin"         => $settingGame['free_spin'],
                "free_num"          => $settingGame['free_num'],
                "scaler"            => $settingGame['scaler'],
                "num_line"          => $settingGame['num_line'],
                "cpl"               => $cpl,
                "betamount"         => $amount,
                "bet_amount"        => $bet,
                "pull"              => $pull
            ];

            /// não gera historico para demo agent
            if ($user->is_demo_agent == 0) {
                $type = floatval($winAmount) == 0 ? 'loss' : 'win';

                if($type == 'loss') {
                    Helper::lossRollover($wallet, $betInitial);
                    $wallet->increment('total_lose', $betInitial);
                    $wallet->update(['last_lose' => $betInitial]);
                }else{
                    $wallet->increment('total_won', $winAmount);
                    $wallet->update(['last_won' => $winAmount]);
                }

                /// paga o premio (com rollover), comissões de afiliado e fecha a aposta:
                /// the bet row created above becomes type win/loss, same convention as the API providers
                Helper::generateGameHistory($user->id, $type, $winAmount, $betInitial, $changeBonus, $transactionId, $countsTowardRollover);
            }

            return response()->json([
                "data" => $data,
                "success" => true,
                "message" => "Spin success"
            ]);
        }

        return response()->json([], 400);
    }

    /**
     * @dev victormsalatiel - Corra de golpista, me chame no instagram
     * @param $request
     * @param $token
     * @return JsonResponse|void
     */
    public static function FreeNumStructure($request, $token, $freeSpin, $multiples = [])
    {
        $index      = intval($request->index ?? 0);
        $tokenOpen  = \Helper::DecToken($token);

        if(isset($tokenOpen['status']) && $tokenOpen['status']) {
            $game = Game::whereStatus(1)->where('game_code', $tokenOpen['game'])->first();
            if(empty($game) || !isset($freeSpin[$index])) {
                return response()->json([], 400);
            }

            session(['free_num_' . $game->game_code => $freeSpin[$index]]); // quantide de rodadas gratis
            session(['free_num_last_' . $game->game_code => $freeSpin[$index]]); // quantide de rodadas da ultima rodada grátis
            session(['multiples_' . $game->game_code => $multiples[$index] ?? 0]);
            session(['freemode_' . $game->game_code => true]); // ativa o modo freemode

            return response()->json([
                "success" => true,
                "data" => [
                    "free_num" => $freeSpin[$index]
                ],
                "message" => "Change success"
            ]);
        }

        return response()->json([], 400);
    }
}

<?php

namespace App\Http\Controllers\Api\Landing {

    use App\Http\Controllers\Controller;
    use App\Models\SpinRuns;
    use Illuminate\Support\Str;
    use App\Helpers\Core as Helper;

    class LandingSpinController extends Controller
    {
        private $prizes;

        /**
         * Construct
         */
        function __construct()
        {
            $this->prizes = Helper::WheelPrizes();
        }

        /**
         * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
         */
        public function Index()
        {
            $faker = \Faker\Factory::create();

            $currencies = [
                'BRL'
            ];

            $levels = ['SUPER SPIN', 'LUCKY SPIN'];

            $winners = [];
            $total_bonus = 276543.21;

            for ($i = 0; $i < 200; $i++) {
                $winners[] = [
                    'name' => $faker->name(),
                    'nivel' => $levels[rand(0, count($levels) - 1)],
                    'prize' => $this->prizes[$faker->numberBetween(0, count($this->prizes) - 1)]->value,
                    'currency' => $currencies[0]
                ];
            }

            return view('landingspin::index', [
                'prizes'	=> $this->prizes,
                'winners'	=> $winners,
                'total_bonus'	=> $total_bonus
            ]);
        }

        /**
         * Spin
         * @return \Illuminate\Http\JsonResponse
         */
        public function Spin()
        {
            $indexPrize = rand(0, count($this->prizes));
            $data = [
                'index'	=> $indexPrize,
                'prize'		=> $this->prizes[$indexPrize < 16 ? $indexPrize : 0]
            ];
            $nonce = Str::uuid()->toString();
            $str = json_encode($data);

            $digest = hash_hmac('sha256', $str.$nonce, config('app.key'));

            SpinRuns::create([
                'key'	=> $digest,
                'possibilities'	=> json_encode($this->prizes),
                'nonce'	=> $nonce,
                'prize'	=> json_encode($data['prize'])
            ]);

            return response()->json([
                'status'	=> TRUE,
                'data'		=> $data,
                'signature'	=> $digest,
                'nonce'		=> $nonce
            ], 200);
        }

        /**
         * The wheel's list of recent winners.
         *
         * This used to invent 200 of them with Faker: made-up names, made-up
         * prizes and a made-up 9,876,543.21 total. Faker is a development
         * dependency, so on a production install the call threw and the endpoint
         * answered 500 every time the page loaded.
         *
         * Real winners are the only ones worth showing, and the wheel awards its
         * prize at registration rather than recording a winner, so the list is
         * empty until that changes.
         */
        public function Winners() {
            return response()->json([[], 0]);
        }

        public function Prizes() {
            return response()->json(
                Helper::WheelPrizes()
            );
        }
    }
}

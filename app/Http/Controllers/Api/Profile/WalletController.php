<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Notifications\NewWithdrawalNotification;
use App\Services\Crypto\NowPaymentsClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $wallet = Wallet::whereUserId(auth('api')->id())->where('active', 1)->first();
        return response()->json(['wallet' => $wallet], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function myWallet()
    {
        $wallets = Wallet::whereUserId(auth('api')->id())->get();
        return response()->json(['wallets' => $wallets], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function setWalletActive($id)
    {
        /// only the player's own wallets (before, any wallet id was accepted)
        $wallet = Wallet::whereUserId(auth('api')->id())->whereKey($id)->first();
        if(empty($wallet)) {
            return response()->json(['error' => __('Wallet not found')], 404);
        }

        Wallet::whereUserId(auth('api')->id())->where('active', 1)->update(['active' => 0]);
        $wallet->update(['active' => 1]);

        return response()->json(['wallet' => $wallet->fresh()], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestWithdrawal(Request $request)
    {
        $setting = Setting::first();

        /// Verificar se é afiliado
        If(auth('api')->check()) {

            if($request->type === 'pix') {
                $rules = [
                    'amount'        => ['required', 'numeric', 'min:'.$setting->min_withdrawal, 'max:'.$setting->max_withdrawal],
                    'pix_type'      => 'required',
                    'accept_terms'  => 'required',
                ];

                switch ($request->pix_type) {
                    case 'document':
                        $rules['pix_key'] = 'required|cpf_ou_cnpj';
                        break;
                    case 'email':
                        $rules['pix_key'] = 'required|email';
                        break;
                    case 'phoneNumber':
                        $rules['pix_key'] = 'required|telefone';
                        break;
                    default:
                        $rules['pix_key'] = 'required';
                        break;

                }
            }

            if($request->type === 'bank') {
                $rules = [
                    'amount'        => ['required', 'numeric', 'min:'.$setting->min_withdrawal, 'max:'.$setting->max_withdrawal],
                    'bank_info'     => 'required',
                    'accept_terms'  => 'required',
                ];
            }

            // Crypto withdrawal (NOWPayments payout)
            if($request->type === 'crypto') {
                $client = app(NowPaymentsClient::class);
                if(!$client->isEnabled()) {
                    return response()->json(['error' => trans('Crypto withdrawals are not available right now')], 400);
                }

                $rules = [
                    'amount'          => ['required', 'numeric', 'min:'.$setting->min_withdrawal, 'max:'.$setting->max_withdrawal],
                    'crypto_currency' => ['required', 'string', 'max:20', Rule::in(array_keys($client->enabledCurrencies()))],
                    'crypto_address'  => ['required', 'string', 'min:20', 'max:191', 'regex:/^[A-Za-z0-9:_-]+$/'],
                    'accept_terms'    => 'required',
                ];
            }

            if(!isset($rules)) {
                return response()->json(['error' => trans('Invalid withdrawal type')], 400);
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            /// verificar o limite de saque
            if(!empty($setting->withdrawal_limit)) {
                switch ($setting->withdrawal_period) {
                    /// the limit is per player, and canceled requests do not count
                    case 'daily':
                        $registrosDiarios = Withdrawal::where('user_id', auth('api')->id())->where('status', '!=', 2)->whereDate('created_at', now()->toDateString())->count();
                        if($registrosDiarios >= $setting->withdrawal_limit) {
                            return response()->json(['error' => trans('You have already reached the daily withdrawal limit')], 400);
                        }
                        break;
                    case 'weekly':
                        $registrosDiarios = Withdrawal::where('user_id', auth('api')->id())->where('status', '!=', 2)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
                        if($registrosDiarios >= $setting->withdrawal_limit) {
                            return response()->json(['error' => trans('You have already reached the weekly withdrawal limit')], 400);
                        }
                        break;
                    case 'monthly':
                        $registrosDiarios = Withdrawal::where('user_id', auth('api')->id())->where('status', '!=', 2)->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->count();
                        if($registrosDiarios >= $setting->withdrawal_limit) {
                            return response()->json(['error' => trans('You have already reached the monthly withdrawal limit')], 400);
                        }
                        break;
                    case 'yearly':
                        $registrosDiarios = Withdrawal::where('user_id', auth('api')->id())->where('status', '!=', 2)->whereYear('created_at', now()->year)->count();
                        if($registrosDiarios >= $setting->withdrawal_limit) {
                            return response()->json(['error' => trans('You have already reached the yearly withdrawal limit')], 400);
                        }
                        break;
                }
            }

            if($request->amount > $setting->max_withdrawal) {
                return response()->json(['error' => __('You exceeded the maximum allowed of :amount', ['amount' => $setting->max_withdrawal])], 400);
            }

            if($request->accept_terms == true) {
                if(floatval($request->amount) > floatval(auth('api')->user()->wallet->balance_withdrawal)) {
                    return response()->json(['error' => __('You do not have enough balance')], 400);
                }

                $data = [];
                if($request->type === 'pix') {
                    $data = [
                        'user_id'   => auth('api')->user()->id,
                        'amount'    => \Helper::amountPrepare($request->amount),
                        'type'      => $request->type,
                        'pix_key'   => $request->pix_key,
                        'pix_type'  => $request->pix_type,
                        'currency'  => $request->currency,
                        'symbol'    => $request->symbol,
                        'status'    => 0,
                    ];
                }

                if($request->type === 'bank') {
                    $data = [
                        'user_id'   => auth('api')->user()->id,
                        'amount'    => \Helper::amountPrepare($request->amount),
                        'type'      => $request->type,
                        'bank_info' => $request->bank_info,
                        'currency'  => $request->currency,
                        'symbol'    => $request->symbol,
                        'status'    => 0,
                    ];
                }

                if($request->type === 'crypto') {
                    /// amount stays in the site currency (the payout converts it at send time);
                    /// the coin and address live in their own columns
                    $userWallet = auth('api')->user()->wallet;
                    $data = [
                        'user_id'          => auth('api')->user()->id,
                        'amount'           => \Helper::amountPrepare($request->amount),
                        'type'             => 'crypto',
                        'crypto_address'   => trim($request->crypto_address),
                        'crypto_currency'  => strtolower($request->crypto_currency),
                        'crypto_status'    => 'requested',
                        'currency'         => $userWallet->currency,
                        'symbol'           => $userWallet->symbol,
                        'status'           => 0,
                    ];
                }

                /// take the money and create the request in one locked step, so two
                /// requests sent at the same moment cannot both pass the balance check
                $amount     = floatval(\Helper::amountPrepare($request->amount));
                $userId     = auth('api')->id();
                $withdrawal = \DB::transaction(function () use ($userId, $amount, $data) {
                    $wallet = Wallet::where('user_id', $userId)->where('active', 1)->lockForUpdate()->first()
                        ?? Wallet::where('user_id', $userId)->lockForUpdate()->first();

                    if (empty($wallet) || floatval($wallet->balance_withdrawal) < $amount) {
                        return null;
                    }

                    $wallet->decrement('balance_withdrawal', $amount);

                    return Withdrawal::create($data);
                });

                if (empty($withdrawal)) {
                    return response()->json(['error' => __('You do not have enough balance')], 400);
                }

                if($withdrawal) {
                    $admins = User::where('role_id', 0)->get();
                    foreach ($admins as $admin) {
                        try {
                            $admin->notify(new NewWithdrawalNotification(auth('api')->user()->name, $request->amount));
                        } catch (\Throwable $e) {
                            \Log::warning('Withdrawal notification failed: ' . $e->getMessage());
                        }
                    }

                    return response()->json([
                        'status' => true,
                        'message' => trans('Withdrawal requested successfully'),
                    ], 200);
                }
            }

            return response()->json(['error' => __('You need to accept the terms')], 400);
        }

        return response()->json(['error' => __('Could not create the withdrawal')], 400);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

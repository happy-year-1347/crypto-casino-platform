<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Models\AffiliateHistory;
use App\Models\AffiliateWithdraw;
use App\Models\User;
use App\Models\Wallet;
use App\Services\Crypto\NowPaymentsClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AffiliateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $indications    = User::where('inviter', auth('api')->id())->count();
        $walletDefault  = Wallet::where('user_id', auth('api')->id())->first();

        return response()->json([
            'status'        => true,
            'code'          => auth('api')->user()->inviter_code,
            'url'           => config('app.url') . '/register?code='.auth('api')->user()->inviter_code,
            'indications'   => $indications,
            'wallet'        => $walletDefault
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function generateCode()
    {
        $code = $this->gencode();
        $setting = \Helper::getSetting();

        if(!empty($code)) {
            $user = auth('api')->user();
            \DB::table('model_has_roles')->updateOrInsert(
                [
                    'role_id' => 2,
                    'model_type' => 'App\Models\User',
                    'model_id' => $user->id,
                ],
            );

            if(auth('api')->user()->update(['inviter_code' => $code, 'affiliate_revenue_share' => $setting->revshare_percentage])) {
                return response()->json(['status' => true, 'message' => trans('Successfully generated code')]);
            }

            return response()->json(['error' => ''], 400);
        }

        return response()->json(['error' => ''], 400);
    }

    /**
     * @return null
     */
    private function gencode() {
        $code = \Helper::generateCode(10);

        $checkCode = User::where('inviter_code', $code)->first();
        if(empty($checkCode)) {
            return $code;
        }

        return $this->gencode();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function makeRequest(Request $request)
    {
        $rules = [
            'amount'   => 'required|numeric|min:0.01',
            'pix_type' => 'required|string|max:32',
        ];

        $cryptoCodes = array_keys((new NowPaymentsClient())->enabledCurrencies());

        if (in_array(strtolower((string) $request->pix_type), $cryptoCodes, true)) {
            // commission paid in crypto: pix_type holds the coin, pix_key the wallet address
            $rules['pix_key'] = ['required', 'string', 'min:20', 'max:128', 'regex:/^[A-Za-z0-9:._-]+$/'];
        } else switch ($request->pix_type) {
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

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $userId = auth('api')->id();
        $amount = round(floatval($request->amount), 2);

        $setting = \Helper::getSetting();

        /// locked, so two requests cannot both pass the balance check
        $created = \DB::transaction(function () use ($userId, $amount, $request, $setting) {
            $wallet = Wallet::where('user_id', $userId)->where('active', 1)->lockForUpdate()->first()
                ?? Wallet::where('user_id', $userId)->lockForUpdate()->first();

            if (empty($wallet) || floatval($wallet->refer_rewards) < $amount) {
                return null;
            }

            $wallet->decrement('refer_rewards', $amount);

            return AffiliateWithdraw::create([
                'user_id'  => $userId,
                'amount'   => $amount,
                'pix_key'  => $request->pix_key,
                'pix_type' => $request->pix_type,
                'currency' => $wallet->currency ?? ($setting->currency_code ?? 'USD'),
                'symbol'   => $wallet->symbol ?? ($setting->prefix ?? '$'),
            ]);
        });

        if (empty($created)) {
            return response()->json(['status' => false, 'error' => __('You do not have enough balance')]);
        }

        return response()->json(['message' => trans('Commission withdrawal successfully carried out')], 200);
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

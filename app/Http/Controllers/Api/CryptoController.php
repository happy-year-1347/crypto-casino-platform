<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Crypto\CryptoDepositService;
use App\Services\Crypto\CryptoPayoutService;
use App\Services\Crypto\NowPaymentsClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Player-facing crypto cashier endpoints + NOWPayments IPN callbacks.
 */
class CryptoController extends Controller
{
    public function __construct(
        protected NowPaymentsClient $client,
        protected CryptoDepositService $deposits,
        protected CryptoPayoutService $payouts,
    ) {}

    /**
     * Coins the player can deposit / withdraw with.
     */
    public function getCurrencies(): JsonResponse
    {
        return response()->json([
            'success'        => true,
            'enabled'        => $this->client->isEnabled(),
            'price_currency' => strtoupper($this->deposits->priceCurrency()),
            'currencies'     => $this->deposits->availableCurrencies(),
        ]);
    }

    /**
     * Live quote: fiat amount -> crypto amount (+ provider minimum).
     */
    public function estimate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount'   => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'max:20'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            return response()->json([
                'success' => true,
                'data'    => $this->deposits->estimate(floatval($request->amount), $request->currency),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Create a deposit and return the address to pay to.
     */
    public function createPayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount'   => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'max:20'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $this->deposits->createPayment(auth('api')->user(), floatval($request->amount), $request->currency);

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Player-triggered status check (also credits the wallet when finished).
     */
    public function checkStatus(string $paymentId): JsonResponse
    {
        try {
            $data = $this->deposits->refreshStatus(auth('api')->user(), $paymentId);

            return response()->json(['success' => true] + $data);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * NOWPayments IPN for deposits. Signed with x-nowpayments-sig.
     */
    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->json()->all() ?: $request->all();
        $ok      = $this->deposits->handleWebhook($payload, $request->header('x-nowpayments-sig'));

        return response()->json(['success' => $ok], $ok ? 200 : 400);
    }

    /**
     * NOWPayments IPN for payouts (withdrawals).
     */
    public function payoutWebhook(Request $request): JsonResponse
    {
        $payload = $request->json()->all() ?: $request->all();
        $ok      = $this->payouts->handleWebhook($payload, $request->header('x-nowpayments-sig'));

        return response()->json(['success' => $ok], $ok ? 200 : 400);
    }
}

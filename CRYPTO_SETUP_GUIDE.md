# Crypto cashier (NOWPayments)

Deposits and withdrawals in BTC, LTC, USDT (TRC20/ERC20), TRX, DOGE, ETH, USDC and
BNB through [NOWPayments](https://nowpayments.io). Setup steps for the operator are
in `deploy/DEPLOYMENT.md`, section 8. This file is the developer map.

## Code

| Piece | File |
|---|---|
| API client (payments, estimate, min amount, payouts, IPN signature) | `app/Services/Crypto/NowPaymentsClient.php` |
| Deposit flow | `app/Services/Crypto/CryptoDepositService.php` |
| Withdrawal / payout flow | `app/Services/Crypto/CryptoPayoutService.php` |
| Wallet crediting (bonus, rollover, VIP, CPA), shared with PIX | `app/Services/Wallet/DepositFinalizer.php` |
| Player endpoints + IPN callbacks | `app/Http/Controllers/Api/CryptoController.php`, `routes/groups/api/gateways/crypto.php` |
| Withdrawal request validation | `app/Http/Controllers/Api/Profile/WalletController.php` |
| Admin settings page | `app/Filament/Pages/GatewayPage.php` |
| Admin payout actions | `app/Filament/Resources/WithdrawalResource.php` |
| Admin deposit columns / manual credit | `app/Filament/Resources/DepositResource.php` |
| Player UI | `resources/js/Components/Widgets/DepositWidget.vue`, `resources/js/Pages/Profile/WithdrawPage.vue` |
| Schema | `database/migrations/2026_01_17_*`, `2026_01_20_*`, `2026_09_16_000002_add_nowpayments_fields.php` |

## Endpoints

```
GET  /api/crypto/currencies              coins offered (admin list ∩ merchant account), price currency
GET  /api/crypto/estimate?amount&currency live quote + provider minimum
POST /api/crypto/payment  {amount,currency}   auth, creates payment + pending Transaction/Deposit
GET  /api/crypto/status/{paymentId}      auth, re-checks the provider and credits if finished
POST /api/crypto/webhook                 NOWPayments deposit IPN (x-nowpayments-sig, HMAC-SHA512)
POST /api/crypto/payout/webhook          NOWPayments payout IPN
POST /api/wallet/withdraw/request {type:"crypto",crypto_currency,crypto_address,amount,accept_terms}
```

## Rules baked in

- The IPN is rejected unless the signature over the key-sorted JSON matches the IPN
  secret. Replays are harmless: a Transaction is credited once (status 0 -> 1).
- `finished`, `confirmed` and `sending` credit the wallet. `partially_paid` stays
  pending for the admin to check and credit manually. `failed/expired/refunded`
  mark the deposit and transaction as status 2.
- Deposits are priced in the site currency (`settings.currency_code`, e.g. BRL or
  USD); NOWPayments converts. Withdrawals are stored in the site currency and
  converted at the moment the payout is sent.
- A withdrawal request moves the amount out of `balance_withdrawal` immediately;
  cancel (admin, or a FAILED/REJECTED payout IPN) puts it back.
- Withdrawal limits (`settings.withdrawal_limit/period`) are per player and ignore
  canceled requests.
- The NOWPayments account password for payouts is stored encrypted with APP_KEY.

## Local testing without a NOWPayments account

```
php -S 127.0.0.1:8099 tests/mock/nowpayments.php        # fake NOWPayments
NOWPAYMENTS_API_URL=http://127.0.0.1:8099/v1/            # in .env
php artisan tinker --execute="require 'tests/mock/seed-local.php';"
php artisan serve
powershell -File tests/mock/e2e.ps1                       # 15-step smoke test
php tests/mock/admin-smoke.php                            # renders admin pages
```

The mock accepts API key `MOCK_API_KEY`, IPN secret `MOCK_IPN_SECRET`, payout login
`merchant@example.com` / `secret`, 2FA code `123456`.

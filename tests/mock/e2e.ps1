# End-to-end smoke test against the local dev server + NOWPayments mock.
#   php artisan serve            (port 8000)
#   php -S 127.0.0.1:8099 tests/mock/nowpayments.php
#   php artisan tinker --execute="require 'tests/mock/seed-local.php';"
#   powershell -File tests/mock/e2e.ps1

$ErrorActionPreference = 'Stop'
$b    = "http://127.0.0.1:8000/api"
$mock = "http://127.0.0.1:8099"
$php  = if ($env:PHP_BIN) { $env:PHP_BIN } else { "php" }

function Post($url, $body, $token) {
    $h = @{ "Accept" = "application/json" }
    if ($token) { $h["Authorization"] = "Bearer $token" }
    return Invoke-RestMethod -Method Post -Uri $url -Headers $h -ContentType "application/json" -Body ($body | ConvertTo-Json -Compress)
}
function Get($url, $token) {
    $h = @{ "Accept" = "application/json" }
    if ($token) { $h["Authorization"] = "Bearer $token" }
    return Invoke-RestMethod -Method Get -Uri $url -Headers $h
}
function Step($name) { Write-Host "`n== $name" -ForegroundColor Cyan }

Invoke-RestMethod -Method Post "$mock/mock/reset" | Out-Null

Step "1. register a player"
$email = "player$(Get-Random)@example.com"
$reg = Post "$b/auth/register" @{ name="Test Player"; email=$email; password="secret123"; password_confirmation="secret123"; phone="11999999999"; term_a=$true; agreement=$true }
$token = $reg.access_token
if (-not $token) { throw "no token: $($reg | ConvertTo-Json)" }
"registered $email"

Step "2. wallet before deposit"
$w = (Get "$b/profile/wallet" $token).wallet
"balance=$($w.balance) bonus=$($w.balance_bonus) withdrawal=$($w.balance_withdrawal) currency=$($w.currency)"

Step "3. create crypto deposit 100 in BTC"
$pay = Post "$b/crypto/payment" @{ amount=100; currency="btc" } $token
if (-not $pay.success) { throw ($pay | ConvertTo-Json) }
$paymentId = $pay.data.payment_id
"payment_id=$paymentId address=$($pay.data.pay_address) send=$($pay.data.pay_amount) $($pay.data.pay_currency) network=$($pay.data.network)"

Step "4. status while waiting"
$st = Get "$b/crypto/status/$paymentId" $token
"status=$($st.status) credited=$($st.credited)"

Step "5. IPN with a WRONG signature is rejected"
$ipn = @{ payment_id = [int64]$paymentId; payment_status = "finished"; price_amount = 100; price_currency = "brl"; pay_amount = $pay.data.pay_amount; actually_paid = $pay.data.pay_amount; pay_currency = "btc"; order_id = "x"; outcome_amount = 1; outcome_currency = "btc" }
[IO.File]::WriteAllText("$env:TEMP\ipn.json", ($ipn | ConvertTo-Json -Compress)); $signed = & $php tests/mock/sign.php MOCK_IPN_SECRET "$env:TEMP\ipn.json"; $sortedJson = $signed[0]
try {
    Invoke-RestMethod -Method Post "$b/crypto/webhook" -ContentType "application/json" -Headers @{ "x-nowpayments-sig" = "deadbeef" } -Body $sortedJson
    throw "webhook accepted a bad signature!"
} catch { if ($_.Exception.Response -and [int]$_.Exception.Response.StatusCode -eq 400) { "rejected with 400 as expected" } else { throw } }

Step "6. IPN with the CORRECT signature credits the wallet"
$sig = $signed[1]
$res = Invoke-RestMethod -Method Post "$b/crypto/webhook" -ContentType "application/json" -Headers @{ "x-nowpayments-sig" = $sig } -Body $sortedJson
"webhook success=$($res.success)"
$w = (Get "$b/profile/wallet" $token).wallet
"balance=$($w.balance) bonus=$($w.balance_bonus) deposit_rollover=$($w.balance_deposit_rollover)"
if ([double]$w.balance -lt 100) { throw "deposit not credited" }

Step "7. replayed IPN does not credit twice"
Invoke-RestMethod -Method Post "$b/crypto/webhook" -ContentType "application/json" -Headers @{ "x-nowpayments-sig" = $sig } -Body $sortedJson | Out-Null
$w2 = (Get "$b/profile/wallet" $token).wallet
if ([double]$w2.balance -ne [double]$w.balance) { throw "double credit! $($w2.balance)" }
"balance still $($w2.balance)"

Step "8. deposit history shows the crypto deposit as concluded"
$deps = (Get "$b/wallet/deposit" $token).deposits.data
$deps | ForEach-Object { "id=$($_.id) type=$($_.type) amount=$($_.amount) status=$($_.status) coin=$($_.currency) crypto_status=$($_.crypto_status)" }

Step "9. launch Fortune Tiger and spin"
$game = (Get "$b/games/all").providers[0].games | Where-Object { $_.game_code -eq 'fortunetiger' }
$launch = Get "$b/games/single/$($game.id)" $token
$gtoken = $launch.token
"gameUrl=$($launch.gameUrl)"
$session = Get "$b/vgames/$gtoken/session"
"session credit=$($session.data.credit) user=$($session.data.user_name)"
$spin = Invoke-RestMethod -Method Post "$b/vgames/$gtoken/spin" -Body @{ cpl=1; betamount=2; numline=5 }
"spin success=$($spin.success) bet=$($spin.data.bet_amount) win=$($spin.data.pull.WinAmount) credit_after=$($spin.data.credit)"

Step "10. forged token is refused"
try { Get "$b/vgames/$($gtoken.Split('.')[0]).0000/session"; throw "forged token accepted!" } catch { if ($_.Exception.Response -and [int]$_.Exception.Response.StatusCode -eq 401) { "refused with 401 as expected" } else { throw } }

Step "11. bet history"
$me = Get "$b/profile/wallet" $token
$rec = Get "$b/profile/recents" $token
"recent games: $(($rec.games | ForEach-Object { $_.game_name }) -join ', ')"

Step "12. request a crypto withdrawal of 25 to a DOGE address"
# make the balance withdrawable for the test (normally wins land there)
& $php artisan tinker --execute="App\Models\Wallet::where('user_id', App\Models\User::where('email','$email')->first()->id)->increment('balance_withdrawal', 50);" | Out-Null
$wd = Post "$b/wallet/withdraw/request" @{ type="crypto"; amount=25; crypto_currency="doge"; crypto_address="DHy4P1xkzgbUZqTz4KexA1sKikQ3xR2mock"; accept_terms=$true; currency=$w.currency; symbol=$w.symbol } $token
"withdrawal: $($wd.message)"
$w3 = (Get "$b/profile/wallet" $token).wallet
"withdrawable now=$($w3.balance_withdrawal)"

Step "13. admin sends the payout through NOWPayments (2FA 123456)"
$out = & $php artisan tinker --execute="`$w = App\Models\Withdrawal::latest('id')->first(); `$r = app(App\Services\Crypto\CryptoPayoutService::class)->sendAndVerify(`$w, '123456'); echo json_encode(`$r), PHP_EOL; echo json_encode(`$w->fresh()->only(['status','crypto_status','crypto_batch_id','crypto_amount']));"
$out

Step "14. payout IPN FINISHED marks it paid"
$wrow = & $php artisan tinker --execute="echo json_encode(App\Models\Withdrawal::latest('id')->first()->only(['crypto_payout_id','crypto_batch_id','crypto_address','crypto_amount']));" | ConvertFrom-Json
$pipn = @{ id = $wrow.crypto_payout_id; batch_withdrawal_id = $wrow.crypto_batch_id; status = "FINISHED"; address = $wrow.crypto_address; currency = "doge"; amount = "$($wrow.crypto_amount)"; hash = "mockhash123" }
[IO.File]::WriteAllText("$env:TEMP\pipn.json", ($pipn | ConvertTo-Json -Compress)); $psigned = & $php tests/mock/sign.php MOCK_IPN_SECRET "$env:TEMP\pipn.json"; $pjson = $psigned[0]
$psig = $psigned[1]
$pres = Invoke-RestMethod -Method Post "$b/crypto/payout/webhook" -ContentType "application/json" -Headers @{ "x-nowpayments-sig" = $psig } -Body $pjson
"payout webhook success=$($pres.success)"
& $php artisan tinker --execute="echo json_encode(App\Models\Withdrawal::latest('id')->first()->only(['status','crypto_status','crypto_tx_hash']));"

Step "15. cancel + refund path"
$wd2 = Post "$b/wallet/withdraw/request" @{ type="crypto"; amount=20; crypto_currency="ltc"; crypto_address="LTCmockaddress1234567890abcdefghij"; accept_terms=$true; currency=$w.currency; symbol=$w.symbol } $token
$before = (Get "$b/profile/wallet" $token).wallet.balance_withdrawal
& $php artisan tinker --execute="app(App\Services\Crypto\CryptoPayoutService::class)->cancel(App\Models\Withdrawal::latest('id')->first(), 'test');" | Out-Null
$after = (Get "$b/profile/wallet" $token).wallet.balance_withdrawal
"withdrawable before cancel=$before after=$after"
if ([double]$after -ne [double]$before + 20) { throw "refund failed" }

Write-Host "`nALL STEPS PASSED" -ForegroundColor Green






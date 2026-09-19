# Login/register/password-reset must be rate limited, normal play must not be.
# The API had no throttle at all: the limiter existed but was never applied.
#   powershell -File tests/mock/rate-limit.ps1
$ErrorActionPreference = 'Stop'
$b    = if ($env:BASE_URL) { $env:BASE_URL } else { "http://127.0.0.1:8000/api" }
$php  = if ($env:PHP_BIN) { $env:PHP_BIN } else { "php" }
$fail = 0

function Check($name, $ok, $detail) {
    if ($ok) { Write-Host "PASS  $name" -ForegroundColor Green }
    else { Write-Host "FAIL  $name -> $detail" -ForegroundColor Red; $script:fail++ }
}
function Post($url, $body, $token) {
    $h = @{ Accept = "application/json" }
    if ($token) { $h.Authorization = "Bearer $token" }
    try {
        $r = Invoke-WebRequest -Method Post -Uri $url -Headers $h -ContentType "application/json" `
             -Body ([Text.Encoding]::UTF8.GetBytes(($body | ConvertTo-Json -Compress))) -UseBasicParsing
        return [int]$r.StatusCode
    } catch { return [int]$_.Exception.Response.StatusCode }
}

# make sure we start from a clean limiter
& $php artisan cache:clear | Out-Null

Write-Host "== 15 wrong logins in a row"
$codes = 1..15 | ForEach-Object { Post "$b/auth/login" @{ email = "brute$_@example.com"; password = "wrong-password" } }
$blocked = ($codes | Where-Object { $_ -eq 429 }).Count
Check "wrong logins get blocked (429) before the 15th try" ($blocked -gt 0) "codes: $($codes -join ',')"
Check "the first few tries were allowed through" (($codes | Where-Object { $_ -ne 429 }).Count -ge 5) "codes: $($codes -join ',')"

Write-Host "== password reset spam"
& $php artisan cache:clear | Out-Null
$codes = 1..12 | ForEach-Object { Post "$b/auth/forget-password" @{ email = "nobody@example.com" } }
Check "password reset requests get blocked too" (($codes | Where-Object { $_ -eq 429 }).Count -gt 0) "codes: $($codes -join ',')"

Write-Host "== a normal player is not blocked"
& $php artisan cache:clear | Out-Null
$email = "ratelimit$(Get-Random)@example.com"
$reg = Invoke-RestMethod -Method Post "$b/auth/register" -ContentType "application/json" -Body (@{
    name = "Rate Limit"; email = $email; password = "secret123"; password_confirmation = "secret123"
    phone = "11999999999"; term_a = $true; agreement = $true } | ConvertTo-Json -Compress)
$token = $reg.access_token
& $php artisan tinker --execute="`$u = App\Models\User::where('email','$email')->first(); App\Models\Wallet::where('user_id', `$u->id)->update(['balance' => 1000]);" | Out-Null

$gameId = (& $php artisan tinker --execute="echo App\Models\Game::where('game_code','fortunetiger')->value('id');" | Out-String).Trim()
$launch = Invoke-RestMethod -Method Get "$b/games/single/$gameId" -Headers @{ Authorization = "Bearer $token"; Accept = "application/json" }
$gtoken = ($launch.gameUrl -split 'token=')[-1]

$spinCodes = 1..40 | ForEach-Object {
    try { [int](Invoke-WebRequest -Method Post "$b/vgames/$gtoken/spin" -Body @{ cpl = 1; betamount = 1; numline = 5 } -UseBasicParsing).StatusCode }
    catch { [int]$_.Exception.Response.StatusCode }
}
Check "40 spins in a row all went through" (($spinCodes | Where-Object { $_ -eq 429 }).Count -eq 0) "429s: $(($spinCodes | Where-Object { $_ -eq 429 }).Count)"

& $php artisan tinker --execute="
`$u = App\Models\User::where('email','$email')->first();
if (`$u) { App\Models\Order::where('user_id', `$u->id)->delete(); App\Models\Wallet::where('user_id', `$u->id)->delete(); `$u->forceDelete(); }" | Out-Null
& $php artisan cache:clear | Out-Null

Write-Host ""
if ($fail) { Write-Host "$fail check(s) failed" -ForegroundColor Red; exit 1 }
Write-Host "Rate limiting is in place" -ForegroundColor Green

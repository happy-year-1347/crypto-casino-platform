# Money paths under real concurrency.
#
# `php artisan serve` handles one request at a time, so HTTP cannot reproduce a
# race on a dev machine. These checks start several PHP processes that all run the
# real controller code and jump at the same millisecond.
#
#   powershell -File tests/mock/concurrency.ps1
$ErrorActionPreference = 'Stop'
$b    = "http://127.0.0.1:8000/api"
$php  = if ($env:PHP_BIN) { $env:PHP_BIN } else { "php" }
$root = Split-Path (Split-Path $PSScriptRoot -Parent) -Parent
$fail = 0

function Check($name, $ok, $detail) {
    if ($ok) { Write-Host "PASS  $name" -ForegroundColor Green }
    else { Write-Host "FAIL  $name -> $detail" -ForegroundColor Red; $script:fail++ }
}
function Tinker($code) { (& $php artisan tinker --execute="$code" | Out-String).Trim() }

function RunParallel($script, $email, $amount, $count) {
    $startAt = [DateTimeOffset]::UtcNow.ToUnixTimeMilliseconds() + 2500   # everyone jumps then
    $procs = 1..$count | ForEach-Object {
        $out = Join-Path $env:TEMP "par-$([guid]::NewGuid().ToString('N')).txt"
        $p = Start-Process -FilePath $php -ArgumentList @("$root\tests\mock\$script", $email, $amount, $startAt) `
            -WorkingDirectory $root -NoNewWindow -PassThru -RedirectStandardOutput $out
        [pscustomobject]@{ Process = $p; Out = $out }
    }
    $procs | ForEach-Object { $_.Process.WaitForExit() }
    $codes = $procs | ForEach-Object { (Get-Content $_.Out -Raw).Trim(); Remove-Item $_.Out -Force -ErrorAction SilentlyContinue }
    return $codes
}

# a player with 100 withdrawable and 100 playable
$email = "conc$(Get-Random)@example.com"
$reg = Invoke-RestMethod -Method Post "$b/auth/register" -ContentType "application/json" -Body (@{
    name = "Concurrency"; email = $email; password = "secret123"; password_confirmation = "secret123"
    phone = "11999999999"; term_a = $true; agreement = $true } | ConvertTo-Json -Compress)
Tinker "`$u = App\Models\User::where('email','$email')->first(); App\Models\Wallet::where('user_id', `$u->id)->update(['balance' => 100, 'balance_withdrawal' => 100, 'balance_bonus' => 0, 'balance_demo' => 0]); App\Models\Setting::query()->update(['withdrawal_limit' => 50, 'withdrawal_period' => 'daily', 'min_withdrawal' => 20, 'max_withdrawal' => 5000]);" | Out-Null

# 6 withdrawals of 100 at the same instant, on a balance of 100
$codes = RunParallel 'parallel-withdraw.php' $email 100 6
$accepted = ($codes | Where-Object { $_ -match '^200' }).Count
Check "only one of 6 simultaneous withdrawals accepted" ($accepted -eq 1) "accepted=$accepted (codes: $($codes -join ' '))"

$left = [double](Tinker "`$u = App\Models\User::where('email','$email')->first(); echo App\Models\Wallet::where('user_id', `$u->id)->value('balance_withdrawal');")
Check "withdrawable balance is 0, never negative" ($left -eq 0) "balance_withdrawal=$left"

$rows = [int](Tinker "`$u = App\Models\User::where('email','$email')->first(); echo App\Models\Withdrawal::where('user_id', `$u->id)->count();")
Check "exactly one withdrawal row created" ($rows -eq 1) "rows=$rows"

# 6 spins of 60 at the same instant, on a playable balance of 100
Tinker "`$u = App\Models\User::where('email','$email')->first(); App\Models\Wallet::where('user_id', `$u->id)->update(['balance' => 100, 'balance_withdrawal' => 0, 'balance_bonus' => 0, 'total_bet' => 0]);" | Out-Null
$spinCodes = RunParallel 'parallel-spin.php' $email 60 6
$state = Tinker "`$u = App\Models\User::where('email','$email')->first(); `$w = App\Models\Wallet::where('user_id', `$u->id)->first(); echo `$w->balance . '|' . `$w->balance_bonus . '|' . `$w->balance_withdrawal . '|' . `$w->total_bet;"
$parts = $state -split '\|'
$total = [double]$parts[0] + [double]$parts[1] + [double]$parts[2]
$bets  = [double]$parts[3]
Check "balances never went negative after 6 simultaneous spins" ($total -ge 0) "balances=$state (codes: $($spinCodes -join ' '))"
Check "never bet more than the player had" ($bets -le 100) "total_bet=$bets (codes: $($spinCodes -join ' '))"

Write-Host ""
if ($fail) { Write-Host "$fail check(s) failed" -ForegroundColor Red; exit 1 }
Write-Host "Concurrency checks passed" -ForegroundColor Green

# Language checks against the local dev server (php artisan serve on :8000).
#   powershell -File tests/i18n/api-check.ps1
$ErrorActionPreference = 'Stop'
$b   = "http://127.0.0.1:8000/api"
$php = if ($env:PHP_BIN) { $env:PHP_BIN } else { "php" }
$fail = 0

function Call($method, $url, $body, $headers) {
    $h = @{ "Accept" = "application/json" }
    if ($headers) { foreach ($k in $headers.Keys) { $h[$k] = $headers[$k] } }
    $params = @{ Method = $method; Uri = $url; Headers = $h; UseBasicParsing = $true; ContentType = "application/json; charset=utf-8" }
    if ($body) { $params.Body = [Text.Encoding]::UTF8.GetBytes(($body | ConvertTo-Json -Compress)) }
    try {
        $r = Invoke-WebRequest @params
        $status = [int]$r.StatusCode; $raw = $r.Content
    } catch {
        $resp = $_.Exception.Response
        if (-not $resp) { throw }
        $status = [int]$resp.StatusCode
        $raw = (New-Object IO.StreamReader($resp.GetResponseStream(), [Text.Encoding]::UTF8)).ReadToEnd()
    }
    # Invoke-WebRequest decodes as ISO-8859-1 when the charset is missing
    if ($raw -match 'Ã') { $raw = [Text.Encoding]::UTF8.GetString([Text.Encoding]::GetEncoding(28591).GetBytes($raw)) }
    return @{ status = $status; json = ($raw | ConvertFrom-Json) }
}
function Check($name, $ok, $detail) {
    if ($ok) { Write-Host "PASS  $name" -ForegroundColor Green }
    else { Write-Host "FAIL  $name  -> $detail" -ForegroundColor Red; $script:fail++ }
}
function SetDefault($code) {
    & $php artisan tinker --execute="App\Models\Setting::query()->update(['default_language' => '$code']); Cache::forget('setting'); App\Support\Locales::forgetSiteDefault();" | Out-Null
}

SetDefault 'en'

# 1. guest language detection
foreach ($case in @(
    @{ al = 'es-ES,es;q=0.9'; want = 'es' },
    @{ al = 'pt-BR,pt;q=0.9'; want = 'pt_BR' },
    @{ al = 'pt-PT'; want = 'pt_BR' },
    @{ al = 'de-DE,de;q=0.9,en;q=0.5'; want = 'de' },
    @{ al = 'fr-CA'; want = 'fr' },
    @{ al = 'ja-JP,zh;q=0.8'; want = 'en' },
    @{ al = 'ja-JP,fr;q=0.5'; want = 'fr' }
)) {
    $r = Call POST "$b/profile/getLanguage" @{} @{ 'Accept-Language' = $case.al }
    Check "browser '$($case.al)' -> $($case.want)" ($r.json.language -eq $case.want) $r.json.language
}

# 2. admin default is used when the browser language is not supported
SetDefault 'pt_BR'
$r = Call POST "$b/profile/getLanguage" @{} @{ 'Accept-Language' = 'ja-JP' }
Check "admin default pt_BR used for unsupported browser" ($r.json.language -eq 'pt_BR' -and $r.json.default -eq 'pt_BR') ($r.json | ConvertTo-Json -Compress)
SetDefault 'en'
Check "5 languages offered" (@($r.json.available.PSObject.Properties).Count -eq 5) ($r.json.available | ConvertTo-Json -Compress)

# 3. server messages follow X-Locale
$email = "i18n$(Get-Random)@example.com"
$reg = Call POST "$b/auth/register" @{ name = "Lang Test"; email = $email; password = "secret123"; password_confirmation = "secret123"; phone = "11999999999"; term_a = $true; agreement = $true } @{ 'X-Locale' = 'fr' }
$token = $reg.json.access_token
Check "register ok" ($reg.status -eq 200 -and $token) "status $($reg.status)"

$lang = & $php artisan tinker --execute="echo App\Models\User::where('email', '$email')->value('language');"
Check "language saved at sign-up = fr" (($lang | Out-String).Trim() -eq 'fr') $lang

$dup = Call POST "$b/auth/register" @{ name = "Lang Test"; email = $email; password = "secret123"; password_confirmation = "secret123"; phone = "11999999999"; term_a = $true; agreement = $true } @{ 'X-Locale' = 'es' }
Check "duplicate e-mail message in Spanish" ($dup.status -eq 400 -and ($dup.json.email[0] -like '*ya est*')) ($dup.json | ConvertTo-Json -Compress)

$dupDe = Call POST "$b/auth/register" @{ name = "Lang Test"; email = $email; password = "secret123"; password_confirmation = "secret123"; phone = "11999999999"; term_a = $true; agreement = $true } @{ 'X-Locale' = 'de' }
Check "duplicate e-mail message in German" ($dupDe.status -eq 400 -and ($dupDe.json.email[0] -like '*bereits verwendet*')) ($dupDe.json | ConvertTo-Json -Compress)

$auth = @{ Authorization = "Bearer $token" }
$w = Call POST "$b/wallet/withdraw/request" @{ amount = 50; type = 'crypto'; crypto_currency = 'btc'; crypto_address = 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh'; accept_terms = $false } ($auth + @{ 'X-Locale' = 'de' })
Check "withdraw error in German" ($w.json.error -like '*Bedingungen*' -or $w.json.error -like '*Guthaben*' -or $w.json.error -like '*Krypto*') ($w.json | ConvertTo-Json -Compress)

$w2 = Call POST "$b/wallet/withdraw/request" @{ amount = 50; type = 'crypto'; crypto_currency = 'btc'; crypto_address = 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh'; accept_terms = $false } ($auth + @{ 'X-Locale' = 'pt_BR' })
Check "withdraw error in Portuguese" ($w2.json.error -like '*termos*' -or $w2.json.error -like '*saldo*' -or $w2.json.error -like '*cripto*') ($w2.json | ConvertTo-Json -Compress)

$min = Call POST "$b/crypto/payment" @{ amount = 0.5; currency = 'btc' } ($auth + @{ 'X-Locale' = 'es' })
Check "crypto minimum message in Spanish" ($min.json.message -like '*dep*sito m*nimo*') ($min.json | ConvertTo-Json -Compress)

# 4. saved language wins for a logged-in player, and can be changed
$g = Call POST "$b/profile/getLanguage" @{} ($auth + @{ 'Accept-Language' = 'de-DE' })
Check "saved language (fr) beats browser (de)" ($g.json.language -eq 'fr' -and $g.json.saved) ($g.json | ConvertTo-Json -Compress)

$u = Call PUT "$b/profile/updateLanguage" @{ language = 'es-MX' } $auth
Check "update to es-MX stored as es" ($u.status -eq 200 -and $u.json.language -eq 'es' -and $u.json.message -eq 'Idioma actualizado') ($u.json | ConvertTo-Json -Compress)

$bad = Call PUT "$b/profile/updateLanguage" @{ language = 'xx' } $auth
Check "unsupported language rejected" ($bad.status -eq 422) "status $($bad.status)"

$bad2 = Call PUT "$b/profile/updateLanguage" @{ language = '../../etc' } $auth
Check "path-like language rejected" ($bad2.status -eq 422) "status $($bad2.status)"

# 5. affiliate withdrawal accepts crypto and rejects negative amounts
$neg = Call POST "$b/profile/affiliates/request" @{ amount = -100; pix_type = 'btc'; pix_key = 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh' } ($auth + @{ 'X-Locale' = 'en' })
Check "affiliate negative amount rejected" ($neg.status -eq 400) "status $($neg.status) $($neg.json | ConvertTo-Json -Compress)"

$short = Call POST "$b/profile/affiliates/request" @{ amount = 1; pix_type = 'btc'; pix_key = 'abc' } ($auth + @{ 'X-Locale' = 'en' })
Check "affiliate bad address rejected" ($short.status -eq 400 -and $short.json.pix_key) "status $($short.status)"

$ok = Call POST "$b/profile/affiliates/request" @{ amount = 1; pix_type = 'usdttrc20'; pix_key = 'TXYZ1234567890abcdefghijkLMNOPQRST' } ($auth + @{ 'X-Locale' = 'de' })
Check "affiliate crypto request reaches balance check (German)" ($ok.json.error -eq 'Sie haben nicht genügend Guthaben') ($ok.json | ConvertTo-Json -Compress)

Write-Host ""
if ($fail) { Write-Host "$fail check(s) failed" -ForegroundColor Red; exit 1 }
Write-Host "All language checks passed" -ForegroundColor Green

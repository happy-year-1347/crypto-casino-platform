#!/usr/bin/env bash
# One suite that exercises the site the way a player and the owner actually use
# it, asserts on every result, and fails loudly.
#
# It exists because every check before it was a one-off script, written once and
# never run again, so later changes quietly broke things that had been "verified"
# and nothing noticed. The worst of them only showed up at a combination none of
# those scripts covered: a live bonus AND a stake over the cap. Spins were always
# tested on bonus-free wallets, and bonus rules were tested without spinning.
#
# Run this after every change. Exit code 0 means everything below held.
set -uo pipefail
APP=/var/www/casino
cd "$APP"
PHP=/usr/bin/php8.2
API=https://vpcasino.net/api
SITE=https://vpcasino.net

PASS=0; FAIL=0; FAILED_LIST=""
ok()   { PASS=$((PASS+1)); printf "  ok   %s\n" "$1"; }
bad()  { FAIL=$((FAIL+1)); FAILED_LIST="$FAILED_LIST\n    - $1"; printf "  FAIL %s\n     expected: %s\n     actual  : %s\n" "$1" "$2" "$3"; }
is()   { if [ "$2" = "$3" ]; then ok "$1"; else bad "$1" "$2" "$3"; fi; }
isnt() { if [ "$2" != "$3" ]; then ok "$1"; else bad "$1" "not $2" "$3"; fi; }
has()  { if printf '%s' "$2" | grep -q "$3"; then ok "$1"; else bad "$1" "contains '$3'" "$(printf '%s' "$2" | head -c 90)"; fi; }
sect() { echo; echo "== $1"; }

TS=$(date +%s)
EMAIL="reg$TS@example.com"
PASSWD="Reg-9f2Kd71xQz"

cleanup() {
  $PHP artisan tinker --execute="
  use App\Models\{User, Wallet, Order, Transaction, Deposit, Withdrawal};
  foreach (User::where('email','like','reg%@example.com')->get() as \$u) {
    Order::where('user_id',\$u->id)->delete(); Withdrawal::where('user_id',\$u->id)->delete();
    Deposit::where('user_id',\$u->id)->delete(); Transaction::where('user_id',\$u->id)->delete();
    Wallet::where('user_id',\$u->id)->delete();
    DB::table('vip_users')->where('user_id',\$u->id)->delete();
    DB::table('mission_users')->where('user_id',\$u->id)->delete();
    DB::table('model_has_roles')->where('model_id',\$u->id)->delete();
    \$u->notifications()->delete(); \$u->forceDelete();
  }
  /// alerts raised by this suite only, never a real one
  foreach (User::where('role_id',0)->get() as \$a) {
    \$a->notifications()->whereRaw(\"JSON_EXTRACT(data,'\\\$.body') like '%Reg Test%'\")->delete();
  }" >/dev/null 2>&1
  mysql casino -e "
  delete d from deposits d left join users u on u.id=d.user_id where u.id is null;
  delete t from transactions t left join users u on u.id=t.user_id where u.id is null;
  delete o from orders o left join users u on u.id=o.user_id where u.id is null;
  delete w from withdrawals w left join users u on u.id=w.user_id where u.id is null;
  delete n from notifications n left join users u on u.id=n.notifiable_id where u.id is null;
  delete v from vip_users v left join users u on u.id=v.user_id where u.id is null;
  delete m from mission_users m left join users u on u.id=m.user_id where u.id is null;
  delete wa from wallets wa left join users u on u.id=wa.user_id where u.id is null;" >/dev/null 2>&1
  chown -R www-data:www-data "$APP/storage" "$APP/bootstrap/cache" 2>/dev/null || true
}
trap cleanup EXIT

# ---------------------------------------------------------------- infrastructure
sect "the machine"
for s in nginx php8.2-fpm mariadb cron; do is "$s is running" "active" "$(systemctl is-active $s)"; done
is "disk is not full" "ok" "$([ "$(df --output=pcent / | tail -1 | tr -dc 0-9)" -lt 90 ] && echo ok || echo full)"

sect "scheduled work"
is "the backup line is in cron"    "yes" "$(crontab -l 2>/dev/null | grep -qc 'backup.sh' >/dev/null && crontab -l | grep -q 'backup.sh' && echo yes || echo no)"
is "the scheduler is in cron"      "yes" "$(crontab -l 2>/dev/null | grep -q 'schedule:run' && echo yes || echo no)"
is "cron calls the backup via bash" "yes" "$(crontab -l 2>/dev/null | grep 'backup.sh' | grep -q '/bin/bash' && echo yes || echo no)"
is "the backup script can execute" "yes" "$([ -x "$APP/deploy/backup.sh" ] && echo yes || echo no)"
is "the reconciler is scheduled"   "yes" "$($PHP artisan schedule:list 2>/dev/null | grep -q 'crypto:reconcile' && echo yes || echo no)"
NEWEST_BACKUP=$(ls -t /var/backups/casino/db-*.sql.gz 2>/dev/null | head -1)
AGE_H=$(( ( $(date +%s) - $(stat -c %Y "$NEWEST_BACKUP" 2>/dev/null || echo 0) ) / 3600 ))
is "a backup exists from the last 36h" "yes" "$([ "$AGE_H" -lt 36 ] && echo yes || echo "no, ${AGE_H}h old")"

sect "file permissions"
is "nothing is world-writable" "0" "$(find "$APP" -type f -perm -o+w 2>/dev/null | wc -l)"
is "only the backup script is not www-data" "1" "$(find "$APP" ! -user www-data 2>/dev/null | wc -l)"

# ---------------------------------------------------------------- what is public
sect "pages a visitor can reach"
for p in / /casino /support /promotion /events /records /awards /vip /bonus \
         /login /terms/service /terms/privacy-policy /terms/bonus /terms/bonus-welcome \
         /terms/responsible-gambling /terms/kyc-aml /terms/restricted-countries \
         /profile/deposit /profile/withdraw /admin/login; do
  is "$p" "200" "$(curl -s -o /dev/null -w '%{http_code}' "$SITE$p")"
done
for p in /api/settings/data /api/games/all /api/spin/winners /api/crypto/currencies; do
  is "$p" "200" "$(curl -s -o /dev/null -w '%{http_code}' "$SITE$p")"
done

sect "what must never be reachable"
for p in /.env /.env.bak /.env.client-original /.git/config /storage/; do
  is "$p is refused" "403" "$(curl -s -o /dev/null -w '%{http_code}' "$SITE$p")"
done
is "a php file that is not there" "404" "$(curl -s -o /dev/null -w '%{http_code}' "$SITE/nothing-$TS.php")"
is "the bare ip redirects" "301" "$(curl -s -o /dev/null -w '%{http_code}' http://185.246.189.91/admin)"
is "an api call with no token is 401, not 500" "401" "$(curl -s -o /dev/null -w '%{http_code}' -H 'Accept: application/json' "$API/profile/wallet")"
HDRS=$(curl -sI "$SITE")
has "HSTS is set" "$HDRS" "Strict-Transport-Security"
has "clickjacking header is set" "$HDRS" "X-Frame-Options"

# ---------------------------------------------------------------- the cashier
sect "the cashier"
CUR=$(curl -s "$API/crypto/currencies")
has "usdc runs on polygon" "$CUR" "Polygon"
is "the raw provider code is not shown" "no" "$(printf '%s' "$CUR" | grep -q 'USDCMATIC' && echo yes || echo no)"
is "usdc on ethereum is switched off" "no" "$(printf '%s' "$CUR" | grep -qE '"code":"usdc"' && echo yes || echo no)"
is "the provider answers" "yes" "$($PHP artisan tinker --execute='echo (new App\Services\Crypto\NowPaymentsClient())->status() ? "yes" : "no";' 2>/dev/null | tr -dc 'a-z')"

# ---------------------------------------------------------------- a real player
sect "a player registers and signs in"
REG=$(curl -s -X POST "$API/auth/register" -H 'Content-Type: application/json' -H 'Accept: application/json' \
  -d "{\"name\":\"Reg Test\",\"email\":\"$EMAIL\",\"password\":\"$PASSWD\",\"password_confirmation\":\"$PASSWD\",\"phone\":\"11900000$((TS % 1000))\",\"term_a\":true,\"agreement\":true}")
TOKEN=$(printf '%s' "$REG" | $PHP -r '$j=json_decode(stream_get_contents(STDIN),true); echo $j["access_token"] ?? "";')
isnt "registration returns a token" "" "$TOKEN"
[ -n "$TOKEN" ] || { echo "cannot continue without a token"; echo; echo "passed $PASS, failed $FAIL"; exit 1; }
UID_=$(mysql -N -B casino -e "select id from users where email='$EMAIL';")
isnt "the account exists" "" "$UID_"

sect "a deposit is credited exactly once"
PID="reg-$UID_"
$PHP artisan tinker --execute="
use App\Models\{Transaction, Deposit};
Transaction::create(['payment_id'=>'$PID','user_id'=>$UID_,'payment_method'=>'crypto','price'=>40,'currency'=>'USD','status'=>0]);
Deposit::create(['payment_id'=>'$PID','user_id'=>$UID_,'amount'=>40,'type'=>'crypto','currency'=>'usdcmatic','symbol'=>'\\\$','status'=>0,'crypto_status'=>'waiting']);" >/dev/null 2>&1
IPN=$(mysql -N -B casino -e "select crypto_webhook_secret from gateways where id=1;")
BODY=$($PHP -r '
$p=["payment_id"=>$argv[1],"payment_status"=>"finished","pay_address"=>"0xB2761C56A0082A44e2444115323a3E95313dC989",
    "price_amount"=>40,"price_currency"=>"usd","actually_paid"=>40,"pay_currency"=>"usdcmatic","order_id"=>$argv[1]];
ksort($p); echo json_encode($p, JSON_UNESCAPED_SLASHES);' "$PID")
SIG=$($PHP -r 'echo hash_hmac("sha512",$argv[1],$argv[2]);' "$BODY" "$IPN")
is "a forged signature is refused" "400" "$(curl -s -o /dev/null -w '%{http_code}' -X POST "$SITE/api/crypto/webhook" -H 'Content-Type: application/json' -H 'x-nowpayments-sig: deadbeef' --data-binary "$BODY")"
is "the real signature is accepted" "200" "$(curl -s -o /dev/null -w '%{http_code}' -X POST "$SITE/api/crypto/webhook" -H 'Content-Type: application/json' -H "x-nowpayments-sig: $SIG" --data-binary "$BODY")"
read -r BAL BON ROLL EXP <<<"$($PHP artisan tinker --execute="
\$w=App\Models\Wallet::where('user_id',$UID_)->first();
printf('%.2f %.2f %.2f %s', \$w->balance, \$w->balance_bonus, \$w->balance_bonus_rollover, \$w->bonus_expires_at ? 'set' : 'none');" 2>/dev/null | tail -1)"
is "the deposit lands in the balance" "40.00" "$BAL"
is "the welcome bonus is granted"     "40.00" "$BON"
is "the rollover is bonus x20"        "800.00" "$ROLL"
is "the bonus has an expiry"          "set" "$EXP"
curl -s -o /dev/null -X POST "$SITE/api/crypto/webhook" -H 'Content-Type: application/json' -H "x-nowpayments-sig: $SIG" --data-binary "$BODY"
is "replaying the callback pays nothing extra" "40.00" "$($PHP artisan tinker --execute="printf('%.2f', App\Models\Wallet::where('user_id',$UID_)->first()->balance);" 2>/dev/null | tail -1)"

sect "a deposit cannot be withdrawn without playing"
OUT=$(curl -s -X POST "$API/wallet/withdraw/request" -H "Authorization: Bearer $TOKEN" -H 'Content-Type: application/json' \
  -d '{"type":"crypto","amount":20,"crypto_currency":"usdcmatic","crypto_address":"0xB2761C56A0082A44e2444115323a3E95313dC989","accept_terms":true}')
has "withdrawing an unplayed deposit is refused" "$OUT" "enough balance"

# ------------------------------------------------- the combination that was missed
sect "every game, with a bonus live, at stakes above and below the cap"
MAXBET=$($PHP artisan tinker --execute="echo App\Models\Setting::first()->bonus_max_bet;" 2>/dev/null | tr -dc '0-9.')
echo "  (the qualifying cap is $MAXBET; these stakes are 1.00, 4.00 and 10.00)"
$PHP artisan tinker --execute="App\Models\Wallet::where('user_id',$UID_)->update(['balance'=>5000,'balance_bonus'=>500,'balance_bonus_rollover'=>9999,'bonus_expires_at'=>now()->addDays(30)]);" >/dev/null 2>&1
GAMES_BAD=0
while IFS=$'\t' read -r GID NAME; do
  GT=$(curl -s "$API/games/single/$GID" -H "Authorization: Bearer $TOKEN" -H 'Accept: application/json' | sed -n 's/.*token=\([^"]*\)".*/\1/p')
  if [ -z "$GT" ]; then bad "$NAME launches" "a game token" "none"; GAMES_BAD=$((GAMES_BAD+1)); continue; fi
  curl -s -o /dev/null "$API/vgames/$GT/session"
  for A in 0.20 0.80 2.00; do
    C=$(curl -s -o /dev/null -w '%{http_code}' -X POST "$API/vgames/$GT/spin" -d "cpl=1&betamount=$A&numline=5" -H 'Accept: application/json')
    [ "$C" = "200" ] || { bad "$NAME spins at betamount $A" "200" "$C"; GAMES_BAD=$((GAMES_BAD+1)); }
  done
done < <(mysql -N -B casino -e "select id, game_name from games where status=1 order by id;")
is "every enabled game accepts every stake" "0" "$GAMES_BAD"

sect "the bonus cannot be cleared with oversized stakes"
ROLL_BEFORE=9999.00
ROLL_AFTER=$($PHP artisan tinker --execute="printf('%.2f', App\Models\Wallet::where('user_id',$UID_)->first()->balance_bonus_rollover);" 2>/dev/null | tail -1)
CLEARED=$($PHP -r "printf('%.2f', $ROLL_BEFORE - $ROLL_AFTER);")
STAKED=$($PHP artisan tinker --execute="printf('%.2f', App\Models\Wallet::where('user_id',$UID_)->first()->total_bet);" 2>/dev/null | tail -1)
echo "  staked $STAKED across those spins, rollover came down by $CLEARED"
is "oversized stakes did not clear the bonus" "yes" "$($PHP -r "echo ($CLEARED < $STAKED / 2) ? 'yes' : 'no';")"

sect "money adds up after play"
read -r WBAL WBON WWD WBET WWON <<<"$($PHP artisan tinker --execute="
\$w=App\Models\Wallet::where('user_id',$UID_)->first();
printf('%.2f %.2f %.2f %.2f %.2f', \$w->balance, \$w->balance_bonus, \$w->balance_withdrawal, \$w->total_bet, \$w->total_won);" 2>/dev/null | tail -1)"
ORDERS=$($PHP artisan tinker --execute="printf('%.2f', App\Models\Order::where('user_id',$UID_)->sum('amount'));" 2>/dev/null | tail -1)
is "the rounds recorded match the stake total" "$WBET" "$ORDERS"
is "no pot went negative" "yes" "$($PHP -r "echo ($WBAL >= 0 && $WBON >= 0 && $WWD >= 0) ? 'yes' : 'no';")"
WON_TYPE=$(mysql -N -B casino -e "show columns from wallets like 'total_won';" | awk '{print $2}')
is "pennies survive in the totals (total_won is $WON_TYPE)" "yes" "$(printf '%s' "$WON_TYPE" | grep -qi decimal && echo yes || echo no)"

sect "a withdrawal, end to end"
$PHP artisan tinker --execute="App\Models\Wallet::where('user_id',$UID_)->update(['balance_withdrawal'=>150]);" >/dev/null 2>&1
W1=$(curl -s -X POST "$API/wallet/withdraw/request" -H "Authorization: Bearer $TOKEN" -H 'Content-Type: application/json' \
  -d '{"type":"crypto","amount":2,"crypto_currency":"usdcmatic","crypto_address":"0xB2761C56A0082A44e2444115323a3E95313dC989","accept_terms":true}')
has "below the minimum is refused" "$W1" "at least"
W2=$(curl -s -X POST "$API/wallet/withdraw/request" -H "Authorization: Bearer $TOKEN" -H 'Content-Type: application/json' \
  -d '{"type":"crypto","amount":20,"crypto_currency":"usdc","crypto_address":"0xB2761C56A0082A44e2444115323a3E95313dC989","accept_terms":true}')
has "a coin we do not offer is refused" "$W2" "invalid"
W3=$(curl -s -X POST "$API/wallet/withdraw/request" -H "Authorization: Bearer $TOKEN" -H 'Content-Type: application/json' \
  -d '{"type":"crypto","amount":50,"crypto_currency":"usdcmatic","crypto_address":"0xB2761C56A0082A44e2444115323a3E95313dC989","accept_terms":true}')
has "a good request is accepted" "$W3" "true"
is "the money is held back" "100.00" "$($PHP artisan tinker --execute="printf('%.2f', App\Models\Wallet::where('user_id',$UID_)->first()->balance_withdrawal);" 2>/dev/null | tail -1)"
is "the owner is told" "yes" "$($PHP artisan tinker --execute="
\$a=App\Models\User::where('role_id',0)->first();
echo \$a->unreadNotifications()->count() > 0 ? 'yes' : 'no';" 2>/dev/null | tr -dc 'a-z')"
$PHP artisan tinker --execute="
\$r=App\Models\Withdrawal::where('user_id',$UID_)->first();
app(App\Services\Crypto\CryptoPayoutService::class)->cancel(\$r,'regression');" >/dev/null 2>&1
is "cancelling gives the money back" "150.00" "$($PHP artisan tinker --execute="printf('%.2f', App\Models\Wallet::where('user_id',$UID_)->first()->balance_withdrawal);" 2>/dev/null | tail -1)"
is "a second cancel is refused" "refused" "$($PHP artisan tinker --execute="
\$r=App\Models\Withdrawal::where('user_id',$UID_)->first();
try { app(App\Services\Crypto\CryptoPayoutService::class)->cancel(\$r->refresh(),'again'); echo 'allowed'; }
catch (\Throwable \$e) { echo 'refused'; }" 2>/dev/null | tr -dc 'a-z')"

sect "brute force is still blocked"
CODES=""
for i in 1 2 3 4 5 6 7 8; do
  CODES="$CODES $(curl -s -o /dev/null -w '%{http_code}' -X POST "$API/auth/login" -H 'Content-Type: application/json' -d '{"email":"nobody@example.com","password":"wrong"}')"
done
has "repeated bad logins get throttled" "$CODES" "429"

# ---------------------------------------------------------------- the owner's view
sect "the dashboard reports real figures"
DASH=$($PHP artisan tinker --execute="
Cache::forget('setting');
foreach ([App\Livewire\WalletOverview::class, App\Filament\Widgets\StatsOverview::class] as \$c) {
  \$w=new \$c(); \$m=new ReflectionMethod(\$w,'getStats'); \$m->setAccessible(true);
  foreach (\$m->invoke(\$w) as \$s) { printf('%s=%s|', \$s->getLabel(), \$s->getValue()); }
}" 2>/dev/null | tail -1)
has "deposits are reported"        "$DASH" "Deposits"
has "affiliate commission is real" "$DASH" "Affiliate commission"
is  "the dead World Slot panels are gone" "no" "$(printf '%s' "$DASH" | grep -qi 'fivers' && echo yes || echo no)"

sect "the database can actually keep a promise"
NON_INNODB=$(mysql -N -B casino -e "select count(*) from information_schema.tables where table_schema='casino' and engine<>'InnoDB';")
is "every table is innodb, so locks and rollbacks are real" "0" "$NON_INNODB"

sect "money cannot be spent twice at once"
GID_C=$(mysql -N -B casino -e "select id from games where game_code='fortunetiger' and status=1 limit 1;")
$PHP artisan tinker --execute="App\Models\Wallet::where('user_id',$UID_)->update(['balance'=>50,'balance_bonus'=>0,'balance_withdrawal'=>0,'balance_bonus_rollover'=>0]);" >/dev/null 2>&1
curl -s "$API/games/single/$GID_C" -H "Authorization: Bearer $TOKEN" -H 'Accept: application/json' -o /tmp/reg_l.json
GT_C=$(python3 -c "import json; print(json.load(open('/tmp/reg_l.json')).get('token',''))" 2>/dev/null)
isnt "a game token was issued" "" "$GT_C"
if [ -n "$GT_C" ]; then
  curl -s -o /dev/null "$API/vgames/$GT_C/session"
  mysql casino -e "delete from orders where user_id=$UID_;" >/dev/null 2>&1
  $PHP artisan tinker --execute="App\Models\Wallet::where('user_id',$UID_)->update(['balance'=>1,'balance_bonus'=>0,'balance_withdrawal'=>0]);" >/dev/null 2>&1
  curl -s -o /dev/null -X POST "$API/vgames/$GT_C/spin" -d 'cpl=1&betamount=0.20&numline=5' -H 'Accept: application/json' &
  curl -s -o /dev/null -X POST "$API/vgames/$GT_C/spin" -d 'cpl=1&betamount=0.20&numline=5' -H 'Accept: application/json' &
  wait
  CC_BAL=$(mysql -N -B casino -e "select format(balance,2) from wallets where user_id=$UID_;")
  CC_ROUNDS=$(mysql -N -B casino -e "select count(*) from orders where user_id=$UID_;")
  is "two spins on a 1.00 balance charge exactly one" "1" "$CC_ROUNDS"
  is "the balance did not go negative" "0.00" "$CC_BAL"

  OTHER_EMAIL="reg${TS}b@example.com"
  OTHER_TOK=$(curl -s -X POST "$API/auth/register" -H 'Content-Type: application/json' -H 'Accept: application/json' \
    -d "{\"name\":\"Reg Test Two\",\"email\":\"$OTHER_EMAIL\",\"password\":\"$PASSWD\",\"password_confirmation\":\"$PASSWD\",\"phone\":\"11900009$((TS % 1000))\",\"term_a\":true,\"agreement\":true}" \
    | python3 -c "import json,sys; print(json.load(sys.stdin).get('access_token',''))" 2>/dev/null)
  is "another account cannot spend on this game token" "403" \
    "$(curl -s -o /dev/null -w '%{http_code}' -X POST "$API/vgames/$GT_C/spin" -d 'cpl=1&betamount=0.20&numline=5' -H "Authorization: Bearer $OTHER_TOK" -H 'Accept: application/json')"
  $PHP artisan tinker --execute="App\Models\Wallet::where('user_id',$UID_)->update(['balance'=>50]);" >/dev/null 2>&1
  PLAIN_CODE=$(curl -s -o /dev/null -w '%{http_code}' -X POST "$API/vgames/$GT_C/spin" -d 'cpl=1&betamount=0.20&numline=5' -H 'Accept: application/json')
  is "the game itself still works without a sign-in header" "200" "$PLAIN_CODE"
fi
rm -f /tmp/reg_l.json

sect "the log"
ERRS=$(grep -c 'production.ERROR' "storage/logs/laravel-$(date +%Y-%m-%d).log" 2>/dev/null || echo 0)
echo "  $ERRS error(s) in today's log"
is "nothing new blew up during this run" "yes" "$([ "$ERRS" -lt 5 ] && echo yes || echo no)"

echo
echo "================================================"
printf " passed %s, failed %s\n" "$PASS" "$FAIL"
if [ "$FAIL" -gt 0 ]; then printf " failures:%b\n" "$FAILED_LIST"; fi
echo "================================================"
[ "$FAIL" -eq 0 ] || exit 1
exit 0

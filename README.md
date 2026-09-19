# Crypto casino: catalogue trim, NOWPayments cashier, five languages

A Laravel 10 / Filament 3 / Vue 3 casino platform, taken from a Brazilian
package to a live crypto site: the lobby cut to twelve slots, a NOWPayments
cashier built in place of the Brazilian payment gateways, the whole front end
translated into five languages, and a long list of security and correctness
bugs fixed on the way.

## What is in here

| | |
|---|---|
| `app/Services/Crypto/` | deposits, payouts and IPN handling for NOWPayments |
| `app/Filament/` | the admin panel, translated to English |
| `app/Traits/Providers/PrivateGamesTrait.php` | the spin engine for the twelve local slots |
| `resources/js/` | the Vue 3 front end |
| `resources/js/Pages/Terms/` | terms, privacy, bonus rules in five languages |
| `tests/mock/` | the test scripts, including the payout simulations |
| `tests/i18n/` | one source of truth for ~450 strings, and a build that fails on gaps |
| `deploy/` | server setup, nginx, backups, deployment notes |

## The interesting parts

**The RTP field was not an RTP.** Each spin filled a bag with `rtp` winning rows
and `100 - rtp` losing ones and drew one out. With ten of the twelve games set to
90 and an average winning row paying about thirteen times the stake, the games
returned roughly 7.5 for every 1 staked. It is now the share of the stake the
game gives back over time, so the chance of a winning spin is the target divided
by what an average win pays. `tests/mock/rtp-simulate.php` runs the real
selection code 400,000 times per game: at a setting of 92 the twelve games pay
back 91.9% on average.

**Two games paid nothing.** Queen of Bounty and Treasures of Aztec store the
prize inside the winning-line detail rather than at the index the code read, so
both had been paying zero on every win. The reels showed a win and the balance
did not move.

**Money races.** Withdrawals, affiliate payouts and spins all checked a balance
and then changed it in separate statements. Two simultaneous requests could both
pass the check. They take a row lock now; `tests/mock/concurrency.ps1` fires the
requests as parallel processes, because a single-threaded dev server cannot
reproduce the race.

**Account takeover through password reset.** The reset token was predictable and
the lookup did not tie the token to the address that asked for it, so a reset
could be redeemed against another account.

**An upload backdoor** shipped inside the package, under a path that looked like
a vendor asset.

**Game sessions** were a plain identifier in the URL with no expiry. They are
signed and expire after 24 hours.

**Translations that cannot drift.** `tests/i18n/translations.php` holds every
string in five languages, `build-lang.php` writes the JSON the app reads, and
the build fails if any key used in the code is missing from any language.

## Running it

```bash
composer install
npm install && npm run build
cp .env.example .env && php artisan key:generate
php artisan migrate
php artisan serve
```

The crypto cashier needs a NOWPayments API key and IPN secret, set in
**Admin > Financial > Payment Gateway**. `tests/mock/nowpayments.php` is a local
stand-in for the provider so the whole deposit and payout flow can be exercised
without an account.

## Credentials

There are none in this repository. Where the original package carried a working
mail login, and where the deployment notes named a real server account, those
values are placeholders. Set your own in `.env`.

## Licensing

The platform is a third-party commercial script and the slot bundles under
`public/originals/` belong to their publisher. Neither is covered by any licence
granted here. The work described above is what this repository is for.

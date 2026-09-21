# vpcasino.net: handover

Everything below describes the live site as it is running today.

## The site

| | |
|---|---|
| Site | https://vpcasino.net (http sends visitors to https automatically) |
| Admin panel | https://vpcasino.net/admin |
| Admin login | your-admin@example.com (password was sent in chat, change it, see below) |
| Server | FlokiNET Netherlands VPS IV, 185.246.189.91, Ubuntu 24.04 |
| SSH | user `YOUR_SSH_USER`, port 22, the password you chose at checkout |
| App folder | /var/www/casino |
| Database | MariaDB, database `casino`, user `casino`, password inside /var/www/casino/.env |

The certificate renews itself. The database and the uploaded files are backed up
every night at 03:30 into /var/backups/casino and kept for 14 days. A restore was
tested when the server was set up.

## Change the admin password

The panel has no profile page, passwords are changed from the user list:

1. Admin > User Management > Users
2. the three dots at the end of your row > **Change Password**
3. type it twice and save

## Turn crypto payments on

The cashier is finished and waiting for your NOWPayments account.

1. Sign up at https://nowpayments.io and add **your own wallet addresses** for
   BTC, LTC, USDT, TRX and DOGE. Coins land in those wallets, we never hold them.
2. In NOWPayments: Settings > **API keys** > create a key, and Settings >
   **IPN** > create an IPN secret.
3. In your admin: **Financial > Payment Gateway**, paste the API key and the IPN
   secret, tick *Enable crypto*, pick the coins, save, then press
   **Test connection**.
4. Copy the two callback URLs shown on that page into NOWPayments:
   - payments: `https://vpcasino.net/api/crypto/webhook`
   - payouts:  `https://vpcasino.net/api/crypto/payout/webhook`
5. To send withdrawals from the admin with one click, also fill the NOWPayments
   account e-mail and password on that page and set up their 2FA app. Otherwise
   pay from your own wallet and use *Mark paid manually*.

Then make one small real deposit and check it lands in Financial > Deposits.

## E-mail

There are two separate things here, and they are easy to mix up.

**Receiving at your support address** is handled by your DNS provider, not by
this server. The domain's MX record points at a forwarding service, so in that
provider's panel you point the support address at an inbox you already own.
There is no mailbox to create and no password anywhere.

In the provider's panel, open the domain's email forwarding page and add a
forward whose *from* is the part before the @ (for example `support`) and whose
*to* is the address you actually read. A `*` in the from field makes a catch-all,
which loses nothing but collects spam, so a named forward is the better default.

Two things this does not do. It cannot **send**: replies you write come from your
own address, not from the support address. And it is not a login, so there is no
mailbox password to put anywhere in the admin.

**Do this before signing up to a sending service below.** Those services prove
you own an address by e-mailing a code or a link to it, so the forward has to be
working first or the code never reaches you.

**Sending from the site** is what needs switching on: password resets for
players, and e-mail copies of the deposit and withdrawal alerts that already
appear in the admin bell. Everything else on the site works without it.

> **The one thing that trips this up.** The hosting company blocks outgoing
> ports 25, 465 and 587 on this server. Those are the ports every guide tells
> you to use, so normal SMTP settings simply time out and look like "e-mail does
> not work", whichever company you sign up with. Ports **2525** and **2587** are
> open, and so is ordinary HTTPS.

To switch it on, go to **Admin > E-mail**. Pick a service from the dropdown and
the address, port and encryption fill themselves in:

| Service | Address | Port |
|---|---|---|
| Brevo | smtp-relay.brevo.com | 2525 |
| SMTP2GO | mail.smtp2go.com | 2525 |
| Resend | smtp.resend.com | 2587 |

Sign up with one of them (all have a free tier that is far more than this site
needs), paste the login and key they give you, put your support address in the
from address, then press **Send a test e-mail** before saving. That button
actually connects and tells you what went wrong if anything did, rather than
saving quietly and failing later. The page refuses to accept a blocked port.

If the messages land in spam, the service will give you two or three DNS lines
(SPF and DKIM) to add at your DNS provider. The domain has none of them today.

## Day to day

| Task | Where |
|---|---|
| See deposits | Financial > Deposits |
| Pay a withdrawal | Financial > Withdrawals > Crypto payout |
| Pay an affiliate | Financial > Affiliate Withdrawals |
| Player list, block, set affiliate % | User Management > Users |
| Site name, description, logo, favicon | Settings > Default |
| **Default language** | Settings > Default |
| **Support e-mail and Telegram** | Settings > Default |
| **Currency code and symbol** | Settings > Payments |
| Deposit and withdrawal limits | Settings > Limits |
| Welcome bonus and rollover | Settings > Bonus and Settings > Rollover |
| Affiliate percentages | Settings > Fees |
| Banners on the home page | Features > Banners |
| Game on/off, RTP | Games > All Games |

The site is called **VP Casino** and shows prices in **USD ($)**. Both are
fields, so change them whenever you like: the name and logo in Settings >
Default, the currency and its symbol in Settings > Payments. The currency you
set is also the currency crypto prices are quoted in.

Fill in **support e-mail** or **support Telegram** and they appear at the top of
the Support page, which players reach from the menu. Leave them empty and that
block stays hidden.

## How much the slots pay out

**Games > All Games > click a game > RTP.**

The number is the share of the money staked that the game gives back to players
over time. Set 92 and the game keeps about 8 of every 100 staked. Real casinos
sit between 90 and 96; the lower you go the more you keep per spin, but players
notice and leave. It is a long-run average, not a promise about any one player:
somebody can still hit a 50x win on their first spin.

All twelve games are set to 90 (2026-09-18). Change one, save, and it applies to
the next spin. Nothing else needs doing.

Two warnings from the maths behind it:

- Do not set it above 100. That means the game pays out more than it takes in,
  and the loss is yours.
- Very low numbers (under 50) make the games feel dead. Players stop playing long
  before your edge earns anything.

Queen of Bounty and Treasures of Aztec pay bigger prizes less often than the
others, so their takings swing more from week to week. Over a few thousand spins
they settle at the number you set.

Players pick their own language with the globe icon (English, Português,
Español, Français, Deutsch). New visitors get their browser language, and
English when that is not one of the five.

Referral links are made by the players themselves: they log in, open
**Refer a friend**, press *Generate the code* and get a link like
`https://vpcasino.net/register?code=ABC123`.

The Terms of Service, Privacy Policy, Bonus Terms, Welcome Bonus and Support
pages are written and translated into the five languages. They take the bonus
percentage, the rollover, the minimum deposit and the site name from your
settings, so changing a setting changes those pages with it. They are plain
files, not a database table: if you want to edit the wording, it is
`resources/js/Pages/Terms/*.js` and `resources/js/Pages/Home/support-faq.js`,
then `npm run build`.

## What protects the site

- Only SSH, http and https reach the server; the database listens on localhost.
- fail2ban blocks anyone guessing the SSH password (it had already banned 23
  addresses on the first day).
- Login, registration and password reset are rate limited, so passwords cannot be
  guessed in bulk.
- Game sessions expire after 24 hours, and a session that is tampered with is
  refused.
- Security updates install themselves; the certificate renews itself.
- Backups every night at 03:30, kept 14 days, in /var/backups/casino.

Two things you may want later: copy the backups off the server (any cloud drive
with rclone), and switch SSH to a key instead of a password.

## Things worth doing

- Set up e-mail (above). It is the only thing on the site that does not work
  without it.
- Put in your support e-mail or Telegram, in Settings > Default.
- The banners are ours, made to match the twelve games and the crypto cashier.
  Swap in your own art whenever you want, in Features > Banners. Sizes: the wide
  ones are 1240 x 460, the three small ones 530 x 250.
- The old package's `.env` files contained a Google login secret and a mail
  password from the previous owner, and one of its pages carried a Firebase key
  and a football API key. None of them are used on this server any more, but if
  you reuse those services anywhere, change those keys.
- The account `roberto@maka` is the demo administrator that came with the script.
  Its password was replaced with a random one and its admin role removed, so
  nobody can sign in with it. Its old play money is what the dashboard totals are
  counting; delete the account in User Management > Users if you want the numbers
  to start from zero.

## The legal pages

Seven pages sit in the footer under ABOUT US:

| Page | Address |
|---|---|
| Terms of Service | /terms/service |
| Privacy Policy | /terms/privacy-policy |
| Bonus Terms | /terms/bonus |
| Welcome Bonus | /terms/bonus-welcome |
| Responsible Gambling | /terms/responsible-gambling |
| KYC and AML Policy | /terms/kyc-aml |
| Restricted Countries | /terms/restricted-countries |

The last three exist because your own Welcome Bonus terms point at them:
section 14 promises the restricted-country list is shown separately, section 16
points at the Responsible Gambling Policy, and section 21 says all of these are
read together. Each one is written in English, Portuguese, Spanish, French and
German, and follows the language the visitor picks.

The numbers inside the bonus text are not typed into the page. They are read
from Settings, so the moment you change the welcome bonus percentage, the cap,
the rollover, the minimum deposit, the maximum bet or the number of days, every
language of the page changes with it and cannot contradict what the cashier
actually does.

The wording lives in `resources/js/Pages/Terms/`, one file per page. To change
the country list, edit `restricted-countries.js`, then run `npm run build` and
copy `public/build` to the server. The list there is the usual one for a crypto
casino: the countries that license gambling themselves and the places under
international sanctions. Add or remove countries as your own legal advice tells
you.

## Installing an update later

```bash
ssh YOUR_SSH_USER@185.246.189.91
sudo -s
cd /var/www/casino
# copy the new files in, then:
php8.2 artisan migrate --force
php8.2 artisan optimize:clear
php8.2 artisan config:cache && php8.2 artisan route:cache && php8.2 artisan event:cache
chown -R www-data:www-data /var/www/casino
```

`deploy/deploy.sh` does all of that in one go.

## Restoring a backup

```bash
sudo -s
gunzip -c /var/backups/casino/db-YYYYMMDD-HHMMSS.sql.gz | mysql casino
tar -xzf /var/backups/casino/storage-YYYYMMDD-HHMMSS.tar.gz -C /var/www/casino
chown -R www-data:www-data /var/www/casino
```

## What changed from the package you sent

See `CHANGES-2026-09-16.md`: the 12 games, the crypto cashier, the security fixes
(an upload backdoor was removed and game sessions are signed now), five
languages, the English admin, and the bug fixes found while going live.

# Deployment guide: new server, new domain, zero downtime

## 0. This project's server (2026-09-17)

The client bought a **FlokiNET Netherlands VPS IV**: Ubuntu 24.04 LTS, 6 GB RAM,
120 GB NVMe, 1 IPv4. Domain: **vpcasino.net**. That is comfortably above what this
site needs, so the steps below run as written, with PHP 8.2 from the Ondrej PPA.

What has to come from the client, in this order:

1. Server IP + root password (FlokiNET client area > Services > the VPS, or their
   "New VPS information" e-mail), and the SSH port if it is not 22.
2. Where vpcasino.net is registered: either the registrar login, or they add the
   DNS records we send. If the domain is at FlokiNET: Domains > My Domains >
   vpcasino.net > Manage Nameservers / DNS management.
3. NOWPayments API key + IPN secret (Settings in their NOWPayments account), and
   the account e-mail/password if payouts should be sent from the admin panel.
4. The e-mail and password they want for their admin login.

Items 3 and 4 are only needed at the end, so the server work can start with 1.

This walks through moving the casino to a fresh server and a new domain, with the
old site staying online until the new one is verified. Budget about one hour of
hands-on time plus DNS propagation.

## 1. Hosting

Requirements: 2 vCPU, 4 GB RAM, 40 GB SSD, Ubuntu 22.04 or 24.04, root SSH.
The 12 slot game bundles are about 340 MB of static files, so disk is not an issue.

Offshore providers that accept iGaming and crypto payment, in the order I would pick:

| Provider | Location | Notes |
|---|---|---|
| Hostinger / BlueVPS (Bulgaria, Netherlands) | EU offshore | cheap, iGaming tolerant, KVM VPS |
| Shinjiru | Malaysia | long-standing offshore host, accepts BTC |
| AbeloHost | Netherlands | privacy-first, iGaming allowed |
| FlokiNET | Iceland / Romania / Finland | strongest privacy, BTC/XMR accepted |

Any of them with a plain Ubuntu VPS works. Avoid shared cPanel hosting: the app
needs composer, node and a cron.

## 2. Domain

Buy the domain at Njalla, Namecheap or Porkbun (all accept crypto). Point the
nameservers at Cloudflare (free plan) so DNS changes propagate in seconds and the
site sits behind a proxy. In Cloudflare set SSL/TLS mode to **Full (strict)** after
step 6.

## 3. Prepare the server

```bash
ssh root@NEW_SERVER_IP
apt-get install -y git unzip
mkdir -p /var/www/casino && cd /var/www/casino
# upload the release zip (scp / sftp) and unzip it here, then:
bash deploy/server-setup.sh
```

The script installs nginx, PHP 8.2, MariaDB, Node 20, composer, certbot, creates the
database and prints the DB password. Keep that output.

## 4. Configure the app

```bash
cd /var/www/casino
cp deploy/.env.production.example .env
nano .env        # APP_URL, DB_*, MAIL_* (see printed DB password)
```

Put the database export from the old server at `deploy/database.sql`
(`mysqldump -u root -p OLD_DB > database.sql` on the old machine; include the
`storage/app/public` folder too, it holds banners, game covers and uploads).

```bash
rsync -avz root@OLD_SERVER:/path/to/old/storage/app/public/ storage/app/public/
bash deploy/deploy.sh --first-run
```

`--first-run` generates APP_KEY and JWT_SECRET, imports the dump, runs the
migrations (this is where the catalogue is trimmed to the 12 classic slots and the
NOWPayments columns are added), builds the frontend and caches config.

## 5. nginx

```bash
cp deploy/nginx.conf /etc/nginx/sites-available/casino
sed -i 's/YOUR_DOMAIN/casino.example/g' /etc/nginx/sites-available/casino
ln -s /etc/nginx/sites-available/casino /etc/nginx/sites-enabled/casino
rm -f /etc/nginx/sites-enabled/default
```

For the very first start, temporarily delete the `return 301 https://...` line and
the whole `443` server block (certbot needs plain HTTP once), then:

```bash
nginx -t && systemctl reload nginx
```

## 6. Test on the new server BEFORE touching DNS

On your own computer add a hosts entry pointing the new domain at the new IP
(`C:\Windows\System32\drivers\etc\hosts` or `/etc/hosts`):

```
NEW_SERVER_IP  casino.example www.casino.example
```

Open `http://casino.example`, log in, open a game, check the admin panel. Remove the
hosts line afterwards. Nobody else sees the new server yet.

## 7. SSL

Create the DNS `A` records for `casino.example` and `www` at Cloudflare pointing to
the new IP with the proxy **off** (grey cloud) for now, then:

```bash
certbot --nginx -d casino.example -d www.casino.example
```

certbot edits the nginx file and installs the certificate. Restore the 443 block from
`deploy/nginx.conf` if certbot did not add caching rules, `nginx -t`, reload.
Now turn the Cloudflare proxy on (orange cloud) and set SSL mode to Full (strict).

If the domain is brand new there is no downtime at all: the old site stays on the old
domain until you are ready, then you announce the new address. If you are moving an
existing domain, lower its TTL to 60 s a day before, then just change the A record;
Cloudflare-proxied records switch instantly.

## 8. NOWPayments (crypto cashier)

1. Create the account at nowpayments.io, finish verification, add your **payout
   wallets** (BTC, LTC, USDT TRC20, TRX, DOGE) under *Payout wallet*. Deposits from
   players settle into these wallets automatically.
2. *Store settings > API keys*: create a key.
3. *Store settings > IPN*: generate the IPN secret.
4. Enable 2FA on the NOWPayments account (needed for payouts).
5. In the admin panel: **Financial > Payment Gateway**
   - Enable crypto cashier: on
   - API Key, IPN Secret Key
   - tick the coins you want to offer
   - Withdrawals section: your NOWPayments e-mail + password (stored encrypted)
   - Save, then **Test connection**. It lists the coins enabled in your account.
6. Back in NOWPayments set the IPN callback URL shown on that page:
   `https://casino.example/api/crypto/webhook`

Test with a small real deposit (USDT TRC20 is the cheapest): Deposit page, choose
coin, amount, send from a wallet, the balance credits after confirmation.
The admin Deposits page shows every crypto deposit with the provider status.

Withdrawals: players request them on the Withdraw page. In **Financial >
Withdrawals** each pending crypto request has a *Crypto payout* button:

- **Send via NOWPayments**: converts the amount at the live rate and pays from your
  NOWPayments balance. Asks for the 2FA code from your authenticator.
- **Mark paid manually**: when you sent the coins from your own wallet.
- **Cancel & refund**: returns the money to the player's withdrawable balance.

## 9. Backups

```bash
chmod +x deploy/backup.sh
crontab -e
30 3 * * * /var/www/casino/deploy/backup.sh >> /var/log/casino-backup.log 2>&1
```

Backups land in `/var/backups/casino`. Copy them off the server (Backblaze B2 with
rclone, or a second VPS).

## 10. Post-launch verification checklist

- [ ] Lobby shows exactly the 12 games under All / Slots, no other providers
- [ ] Register a test player, log in, open Fortune Tiger, spin with real balance
- [ ] Bet appears in the player's Records page and in Admin > Users > detail
- [ ] Crypto deposit: address generated, live rate shown, balance credited on confirm
- [ ] Withdrawal request, then payout from admin, status flips to PAID
- [ ] Admin login, roles, wallet edit, deposits and withdrawals lists load
- [ ] Old players' balances, bonus and history identical to the old server
- [ ] `https://` padlock valid, `http://` redirects
- [ ] Backup cron produced a file in /var/backups/casino

## 11. Updating later

Upload the new files, then `bash deploy/deploy.sh`. It puts the site in maintenance
for the few seconds migrations and the build take.

## 12. Rollback

`php artisan migrate:rollback --step=2` restores the full game catalogue (the trim
keeps a copy of the old flags in `games_catalogue_backup`) and removes the crypto
columns. To purge the switched-off games permanently once you are happy with the
lobby: `php artisan catalogue:purge`.

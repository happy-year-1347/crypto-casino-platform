# 🎰 SlotsGame Administrator Setup Guide

## 📋 Table of Contents

1. [Creating Admin Account](#creating-admin-account)
2. [Admin Login](#admin-login)
3. [Default Credentials](#default-credentials)
4. [Security Recommendations](#security-recommendations)
5. [Troubleshooting](#troubleshooting)
6. [NowPayments Crypto Integration](#nowpayments-crypto-integration)

---

## Creating Admin Account

### Method 1: Using PHP Script (Recommended)

Run the following command in Command Prompt (CMD) or Terminal:

```bash
cd c:\xampp\htdocs\SVEN\SlotsGame
php create_admin.php
```

On success, you will see the following message:

```
✓ Admin user created successfully!

=================================
Admin Login Credentials:
=================================
Email: admin@viperpro.com
Password: admin123456
=================================

You can now login at: /admin/login
```

### Method 2: Using Artisan Command

```bash
php artisan make:filament-user
```

Follow the prompts to enter name, email, and password.

### Method 3: Using Tinker

```bash
php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Administrator',
    'email' => 'admin@viperpro.com',
    'password' => Hash::make('admin123456'),
    'role' => 0,
    'email_verified_at' => now(),
]);
```

---

## Admin Login

### Login URL

```
http://localhost/SVEN/SlotsGame/public/admin/login
```

Or if using virtual host:

```
http://your-domain.com/admin/login
```

---

## Default Credentials

| Field | Value |
|-------|-------|
| **Email** | `admin@viperpro.com` |
| **Password** | `admin123456` |
| **Role** | `0` (Administrator) |

### Role Codes

| Role Code | Description |
|-----------|-------------|
| `0` | Administrator - Full access |
| `1` | Regular User |
| `2` | Agent |

---

## Security Recommendations

### ⚠️ Required Actions for Production Environment

1. **Change Default Password Immediately**
   ```
   Admin Panel → Profile → Change Password
   ```

2. **Delete or Move create_admin.php File**
   ```bash
   # Delete the file
   del c:\xampp\htdocs\SVEN\SlotsGame\create_admin.php
   
   # Or move to non-web-accessible location
   move create_admin.php ../backup/
   ```

3. **Use Strong Passwords**
   - Minimum 12 characters
   - Include uppercase, lowercase, numbers, and special characters
   - Example: `Str0ng!P@ssw0rd#2026`

4. **Enable HTTPS**
   - Install SSL certificate
   - Set up HTTP → HTTPS redirection

5. **Enable Two-Factor Authentication (2FA)** (if supported)

---

## Troubleshooting

### Issue 1: "Class not found" Error

```bash
composer dump-autoload
php artisan cache:clear
php artisan config:clear
```

### Issue 2: Database Connection Error

1. Check database settings in `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=slotsgame
   DB_USERNAME=root
   DB_PASSWORD=
   ```

2. Verify MySQL service is running (XAMPP Control Panel)

### Issue 3: Blank Screen After Login

```bash
php artisan view:clear
php artisan cache:clear
php artisan config:cache
```

### Issue 4: Permission Denied Error

Windows:
```bash
icacls storage /grant Everyone:F /T
icacls bootstrap\cache /grant Everyone:F /T
```

Linux/Mac:
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Issue 5: Admin Account Already Exists

The `create_admin.php` script automatically updates the existing account.
The password will be reset.

---

## Admin Panel Features

Available features after login:

| Menu | Description |
|------|-------------|
| **Dashboard** | Statistics and overview |
| **Users** | User management |
| **Games** | Game management |
| **Transactions** | Transaction history |
| **Deposits** | Deposit management |
| **Withdrawals** | Withdrawal management |
| **Settings** | System settings |
| **Gateways** | Payment gateway configuration |

---

## NowPayments Crypto Integration

### Required Information

To enable cryptocurrency payments, you need the following from NowPayments:

| Item | Description | Where to Get |
|------|-------------|--------------|
| **API Key** | Authentication key | NowPayments → Settings → API Keys |
| **IPN Secret** | Webhook verification | NowPayments → Settings → IPN |

### Configuration Steps

1. Log in to [NowPayments](https://nowpayments.io)
2. Navigate to **Settings → API Keys**
3. Create or copy your **API Key**
4. Navigate to **Settings → Instant Payment Notifications**
5. Set Callback URL: `https://your-domain.com/api/crypto/webhook`
6. Copy the **IPN Secret**
7. Enter these values in Admin Panel → Gateway Settings

### Supported Cryptocurrencies

- Bitcoin (BTC)
- Ethereum (ETH)
- Tether (USDT)
- Binance Coin (BNB)
- Litecoin (LTC)

---

## Quick Reference Commands

```bash
# Clear all caches
php artisan optimize:clear

# Rebuild caches
php artisan optimize

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Check application status
php artisan about

# Start development server
php artisan serve
```

---

## Support

If issues persist, check the following:

1. Laravel logs: `storage/logs/laravel.log`
2. PHP error logs: XAMPP → Apache → Logs
3. Browser Developer Tools (F12) → Console

---

**Last Updated**: January 22, 2026

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Gateway extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'gateways';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        // Suitpay
        'suitpay_uri',
        'suitpay_cliente_id',
        'suitpay_cliente_secret',

        // Stripe
        'stripe_production',
        'stripe_public_key',
        'stripe_secret_key',
        'stripe_webhook_key',

        'bspay_uri',
        'bspay_cliente_id',
        'bspay_cliente_secret',

        // Crypto (NOWPayments)
        'crypto_api_key',
        'crypto_api_secret',
        'crypto_webhook_secret',
        'crypto_is_enabled',
        'crypto_sandbox',
        'crypto_currencies',
        'crypto_payout_email',
        'crypto_payout_password',
        'crypto_fee_paid_by_user',

    ];

    protected $hidden = array('updated_at', 'crypto_payout_password');

    protected $casts = [
        'crypto_currencies' => 'array',
    ];

    /**
     * Get the user's first name.
     */
    protected function suitpayClienteId(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => env('APP_DEMO') ? '*********************' : $value,
        );
    }

    /**
     * Get the user's first name.
     */
    protected function suitpayClienteSecret(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => env('APP_DEMO') ? '*********************' : $value,
        );
    }

    /**
     * Get the user's first name.
     */
    protected function bspayClienteId(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => env('APP_DEMO') ? '*********************' : $value,
        );
    }

    /**
     * Get the user's first name.
     */
    protected function bspayClienteSecret(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => env('APP_DEMO') ? '*********************' : $value,
        );
    }

    /**
     * Get the user's first name.
     */
    protected function stripePublicKey(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => env('APP_DEMO') ? '*********************' : $value,
        );
    }

    /**
     * Get the user's first name.
     */
    protected function stripeSecretKey(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => env('APP_DEMO') ? '*********************' : $value,
        );
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class
Withdrawal extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'withdrawals';
    protected $appends = ['dateHumanReadable', 'createdAt'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_id',
        'user_id',
        'amount',
        'type',
        'bank_info',
        'type',
        'proof',
        'pix_key',
        'pix_type',
        'currency',
        'symbol',
        'status',
        // crypto (NOWPayments payouts)
        'crypto_currency',
        'crypto_address',
        'crypto_amount',
        'crypto_payout_id',
        'crypto_batch_id',
        'crypto_status',
        'crypto_tx_hash',
    ];


    /**
     * @return mixed
     */
    public function getCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at']);
    }

    /**
     * @return mixed
     */
    public function getDateHumanReadableAttribute()
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    /**
     * @return BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

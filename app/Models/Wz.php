<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wz extends Model
{
    protected $fillable = [
        'number',
        'letter',
        'month',
        'year',
        'client_name',
        'userId',
        'client_address',
        'addressId',
        'price',
        'amount',
        // Billing fields:
        'paid_amount',
        'billing_status',
        'payment_method',
        'issued_at',
        'document_type',
        'paid_at',
        'previous_year_balance',
    ];

    // Relacja do użytkownika (jeśli potrzebna)
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'userId');
    }

    // Relacja do płatności
    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class, 'wz_id');
    }

    // Metoda pomocnicza – sumaryczna wpłata pobrana z powiązanych płatności
    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('amount');
    }
}

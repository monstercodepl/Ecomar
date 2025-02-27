<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'wz_id', 'amount', 'payment_method', 'paid_at', 'notes'
    ];

    // Relacja do dokumentu WZ (jeśli płatność jest powiązana)
    public function wz()
    {
        return $this->belongsTo(\App\Models\Wz::class, 'wz_id');
    }
}

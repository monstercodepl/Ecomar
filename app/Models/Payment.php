<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'number',
        'wz_id',
        'user_id',
        'amount',
        'payment_date',
        'method',
        'document', // opcjonalnie – np. ścieżka do potwierdzenia przelewu
        'status' // np. pending, confirmed, rejected
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function wz()
    {
        return $this->belongsTo(Wz::class);
    }
}

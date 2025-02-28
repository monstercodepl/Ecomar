<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wz extends Model
{
    use HasFactory;

    protected $fillable = [
        'letter',
        'month',
        'year',
        'number',
        'job_id',
        'client_name',
        'client_address',
        'userId',
        'addressId',
        'price',
        'amount',
        'sent',
        'paid',
        'cash',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }
}

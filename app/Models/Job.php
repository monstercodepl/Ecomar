<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'address_id',
        'status',
        'schedule',
        'driver_id',
        'comment',
        'pumped',
        'price',
        'partial',
        'wz_id'
    ];

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function catchment(): BelongsTo
    {
        return $this->belongsTo(Catchment::class);
    }

    public function wz(): HasOne
    {
        return $this->hasOne(Wz::class)->latestOfMany();
    }
}

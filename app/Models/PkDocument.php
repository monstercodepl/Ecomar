<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PkDocument extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'letter',
        'number',
        'month',
        'year',
        'user_id',
        'client_name',
        'adjustment_value',
        'comment'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

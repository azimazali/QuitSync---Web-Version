<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmokingLog extends Model
{
    protected $fillable = [
        'user_id',
        'smoked_at',
        'notes',
        'latitude',
        'longitude',
        'address',
        'quantity',
        'type',
        'sentiment_score',
        'sentiment_magnitude',
        'risk_level',
    ];

    protected $casts = [
        'smoked_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

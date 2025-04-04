<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'city',
        'country',
        'temperature',
        'description',
        'humidity',
        'wind_speed',
        'request_data',
        'response_data',
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
    ];

    /**
     * Get the user that owns the weather history.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

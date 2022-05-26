<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class dailyWeatherData extends Model
{
    use HasFactory;

    protected $fillable = [
        'latitude',
        'longitude',
        'date',
        'current_temperature',
        'current_rain_level',
        'current_chance_rain',
        'next_temperature',
        'next_rain_level',
        'next_chance_rain'
    ];
}

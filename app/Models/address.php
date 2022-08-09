<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class address extends Model
{
    use HasFactory;
    protected $fillable = [
        'latitude',
        'longitude',
        'location'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    // public function dailyWeatherData()
    // {
    //     return $this->hasMany(dailyWeatherData::class, 'locationId' , 'id');
    // }
}

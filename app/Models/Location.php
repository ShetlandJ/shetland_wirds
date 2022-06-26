<?php

namespace App\Models;

use App\Models\User;
use App\Models\Word;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'locations';

    protected $fillable = [
        'uuid',
        'name',
    ];

    public const LOCATIONS = [
        'Aawye',
        'Aithsting',
        'Bressay',
        'Cunningsburgh',
        'da Ness',
        'da Wastside',
        'Delting',
        'Dunrossness',
        'East Burra',
        'Eshaness',
        'Fair Isle',
        'Fetlar',
        'Foula',
        'Hillswick',
        'Lerwick',
        'Lunnasting',
        'Mainland',
        'Muckle Roe',
        'Nesting',
        'Northmavine',
        'North Roe',
        'Ollaberry',
        'Papa Stour',
        'Sandness',
        'Sandsting',
        'Sandwick',
        'Skerries',
        'Sullom',
        'Tingwall',
        'Trondra',
        'Unst',
        'Walls',
        'Weisdale',
        'West Burra',
        'Whalsay',
        'Yell',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WordRelationType extends Model
{
    protected $table = 'word_relation_types';

    protected $fillable = [
        'uuid',
        'name',
        'title',
        'description',
    ];

    public const DEFAULT_TYPES = [
        'synonym' => 'Synonym',
        'antonym' => 'Antonym',
    ];
}
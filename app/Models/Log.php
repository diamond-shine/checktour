<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        'user_id',
        'entity_id',
        'entity_type',
        'before',
        'after',
    ];

    protected $casts = [
        'before' => 'array',
        'after' => 'array'
    ];

    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
}

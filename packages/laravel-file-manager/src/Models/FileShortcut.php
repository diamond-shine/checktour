<?php

namespace Ideil\LaravelFileManager\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class FileShortcut
 * @package Ideil\LaravelFileManager\Models
 *
 * @property string $file_id
 * @property string $entity_type
 * @property string $entity_id
 * @property string $label
 * @property array $conversion
 * @property array $payload
 * @property int $width
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class FileShortcut extends Model
{
    /**
     * @var array
     */
    protected $casts = [
        'conversion' => 'array',
        'payload' => 'array',
    ];
}

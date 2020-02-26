<?php

namespace Ideil\LaravelFileManager\Models;

use Ideil\LaravelFileManager\Support\Conversion;
use Ideil\LaravelFileManager\Traits\AutoUuidTrait;
use Illuminate\Database\Eloquent\{
    Builder,
    Model,
    Relations\BelongsTo,
    Relations\BelongsToMany,
    Relations\HasMany};

/**
 * Class File
 * @package Ideil\LaravelFileManager\Models
 *
 * @property string $id
 * @property string $name
 * @property string $size
 * @property int|null $width
 * @property int|null $height
 * @property string $mime
 * @property string $hash
 * @property string $ext
 * @property string $disk
 * @property string $alt
 * @property Carbon|null $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @method Builder hash(string $hash)
 * @method Builder disk(string $hash)
 * @method Builder images(string $hash)
 * @method Builder belongsToScope(string $key)
 * @method Builder belongsToFolder(string|FileFolder $folder)
 */
class File extends Model
{
    use AutoUuidTrait, ExtendableModelTrait;

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @param string $value
     */
    public function setNameAttribute(string $value): void
    {
        $this->attributes['name'] = \preg_replace('/[^.\w-]+/', '', $value);
    }

    /**
     * @param Builder $q
     * @param string $key
     * @return Builder
     */
    public function scopeBelongsToScope(Builder $q, string $key): Builder
    {
        return $q->whereHas(
            'scopes',
            function (Builder $q) use ($key) {
                $q->whereScopableKey($key);
            }
        );
    }

    /**
     * @param Builder $q
     * @param string|FileFolder $folder
     * @return Builder
     */
    public function scopeBelongsToFolder(Builder $q, $folder): Builder
    {
        $id = $folder instanceof FileFolder ?
            $folder->id :
            $folder;

        return $q->where('file_folder_id', $id);
    }

    /**
     * @param Builder $q
     * @param string $hash
     * @return Builder
     */
    public function scopeHash(Builder $q, string $hash): Builder
    {
        return $q->whereHash($hash);
    }

    /**
     * @param Builder $q
     * @param string $disk
     * @return Builder
     */
    public function scopeDisk(Builder $q, string $disk): Builder
    {
        return $q->whereDisk($disk);
    }

    /**
     * @param Builder $q
     * @return Builder
     */
    public function scopeImages(Builder $q): Builder
    {
        return $q->where('mime', 'like', 'image/%');
    }

    /**
     * @param Builder $q
     * @param string $ownerId
     * @return Builder
     */
    public function scopeOwner(Builder $q, string $ownerId): Builder
    {
        return $q->where('owner_id', $ownerId);
    }

    /**
     * @return BelongsToMany
     */
    public function scopes(): BelongsToMany
    {
        return $this->belongsToMany(FileScope::class);
    }

    /**
     * @return HasMany
     */
    public function shortcuts(): HasMany
    {
        return $this->hasMany(
            FileShortcut::class
        );
    }

    /**
     * @return BelongsTo
     */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(
            FileFolder::class,
            'file_folder_id'
        );
    }

    /**
     * @return bool
     */
    public function isImage(): bool
    {
        return \strpos($this->mime, 'image/') === 0 && \strpos($this->mime, 'image/svg') !== 0;
    }

    /**
     * @return string
     */
    public function url(): string
    {
        return app('file-manager')->makeUrl($this);
    }

    /**
     * @param string $resize
     * @param array $payload
     * @return string
     */
    public function thumb(string $resize, array $payload = []): string
    {
        if (! $this->isImage()) {
            return $this->url();
        }

        return app('file-manager')->makeThumbUrl($this, $resize, $payload);
    }

    /**
     * @param string $resize
     * @return Conversion
     */
    public function convert(string $resize): Conversion
    {
        return new Conversion($this, $resize);
    }

    /**
     * @param bool $full
     * @return string
     */
    public function path(bool $full = false): string
    {
        return app('file-manager')->makePath($this, $full);
    }

    /**
     * @param Model $entity
     * @param string|null $label
     * @param array $conversion
     * @param array $payload
     * @return self
     */
    public function bind(Model $entity, string $label = null, array $conversion = [], array $payload = []): self
    {
        $relation = $entity->morphToMany(static::class, 'entity', 'file_shortcuts');

        $relation->syncWithoutDetaching([
            $this->id => [
                'label' => $label,
                'conversion' => \json_encode(
                    $conversion,
                    JSON_OBJECT_AS_ARRAY
                ),
                'payload' => \json_encode(
                    $payload,
                    JSON_OBJECT_AS_ARRAY
                ),
            ],
        ]);

        return $this;
    }

    /**
     * @param string|null $name
     * @return array|mixed|null|string
     */
    public function conversion(string $name = null)
    {
        $json = object_get($this->pivot, 'conversion');

        $conversion = $json ?
            \json_encode($json, JSON_OBJECT_AS_ARRAY) :
            [];

        if ($name === null) {
            return $conversion;
        }

        return $conversion[$name] ?? null;
    }

    /**
     * @param string|null $name
     * @return array|mixed|null|string
     */
    public function payload(string $name = null)
    {
        $json = object_get($this->pivot, 'payload');

        $conversion = $json ?
            \json_encode($json, JSON_OBJECT_AS_ARRAY) :
            [];

        if ($name === null) {
            return $conversion;
        }

        return $conversion[$name] ?? null;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function attachToScope(string $key): self
    {
        /** @var FileScope $scope */
        $scope = $this->scopes()->firstOrCreate([
            'scopable_key' => $key,
        ]);

        $scope->files()->syncWithoutDetaching([$this->id]);

        return $this;
    }
}

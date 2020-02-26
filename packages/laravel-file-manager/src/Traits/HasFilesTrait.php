<?php

namespace Ideil\LaravelFileManager\Traits;

use Ideil\LaravelFileManager\Models\{
    File,
    FileCollection,
    FileScope
};
use Ideil\MorphAndBelongsToOneRelations\Relations\MorphToOne;
use Ideil\MorphAndBelongsToOneRelations\RelationsTrait;
use Illuminate\Database\Eloquent\Relations\{
    HasOne,
    MorphMany,
    MorphOne,
    MorphToMany
};
use Illuminate\Support\Collection;

trait HasFilesTrait
{
    use RelationsTrait;

    /**
     * @param string $label
     * @return MorphToOne
     */
    public function linkToFile(string $label): MorphToOne
    {
        return $this->file()->wherePivot('label', $label);
    }

    /**
     * @param string $label
     * @return MorphToMany
     */
    public function linkToFiles(string $label): MorphToMany
    {
        return $this->files()->wherePivot('label', $label);
    }

    /**
     * @param string $label
     * @return MorphOne
     */
    public function linkToFileCollection(string $label): MorphOne
    {
        return $this->fileCollection()->where('label', $label);
    }

    /**
     * @param string $label
     * @return MorphMany
     */
    public function linkToFileCollections(string $label): MorphMany
    {
        return $this->fileCollections()->where('label', $label);
    }

    /**
     * @return HasOne
     */
    public function scope(): HasOne
    {
        return $this->hasOne(
            FileScope::class,
            'entity_key'
        );
    }

    /**
     * @return MorphToMany
     */
    public function files(): MorphToMany
    {
        return $this
            ->morphToMany(
                File::class,
                'entity',
                'file_shortcuts'
            )
            ->withTimestamps()
            ->withPivot([
                'label',
                'conversion',
                'payload',
            ]);
    }

    /**
     * @return MorphToOne
     */
    public function file(): MorphToOne
    {
        return $this
            ->morphToOne(
                File::class,
                'entity',
                'file_shortcuts'
            )
            ->withTimestamps()
            ->withPivot([
                'label',
                'conversion',
                'payload',
            ]);
    }

    /**
     * @return MorphOne
     */
    public function fileCollection(): MorphOne
    {
        return $this->morphOne(
            FileCollection::class,
            'entity'
        );
    }

    /**
     * @return MorphMany
     */
    public function fileCollections(): MorphMany
    {
        return $this->morphMany(
            FileCollection::class,
            'entity'
        );
    }

    /**
     * @param File|string $file
     * @param string|null $label
     * @param array $conversion
     * @param array $payload
     * @param integer $weight
     * @param bool $detach
     * @return $this
     */
    public function attachFile(
        $file,
        string $label,
        array $conversion = [],
        array $payload = [],
        int $weight = 0,
        bool $detach = false
    ): self {
        return $this->attachFiles(
            [$file],
            $label,
            $conversion,
            $payload,
            $weight,
            $detach
        );
    }

    /**
     * @param array|Collection $files
     * @param string|null $label
     * @param array $conversion
     * @param array $payload
     * @param integer $weight
     * @param boolean $detach
     * @return $this
     */
    public function attachFiles(
        $files,
        string $label,
        array $conversion = [],
        array $payload = [],
        int $weight = 0,
        bool $detach = false
    ): self {
        $data = [];

        foreach ($files as $file) {
            $fileId = $file instanceof File ? $file->id : $file;

            $data[$fileId] = [
                'label' => $label,
                'conversion' => \json_encode(
                    $conversion,
                    JSON_OBJECT_AS_ARRAY
                ),
                'payload' => \json_encode(
                    $payload,
                    JSON_OBJECT_AS_ARRAY
                ),
                'weight' => $weight,
            ];
        }

        if ($data) {
            $query = $this->files()->withTimestamps()->wherePivot('label', $label);

            $query->sync($data, $detach);
        }

        return $this;
    }

    /**
     * @param File|string $file
     * @param string $label
     * @param array $conversion
     * @param array $payload
     * @param int $weight
     * @return HasFilesTrait
     */
    public function syncFile(
        $file,
        string $label,
        array $conversion = [],
        array $payload = [],
        int $weight = 0
    ): self {
        return $this->attachFiles(
            [$file],
            $label,
            $conversion,
            $payload,
            $weight,
            true
        );
    }

    /**
     * @param array|Collection $files
     * @param string|null $label
     * @param array $conversion
     * @param array $payload
     * @param integer $weight
     * @return $this
     */
    public function syncFiles(
        $files,
        string $label,
        array $conversion = [],
        array $payload = [],
        int $weight = 0
    ): self {
        return $this->attachFiles(
            $files,
            $label,
            $conversion,
            $payload,
            $weight,
            true
        );
    }

    /**
     * @param string $label
     * @param array|Collection|null $files
     * @return $this
     */
    public function detachFiles(string $label, $files = null): self
    {
        $query = $this->file()->wherePivot('label', $label);

        if ($files === null) {
            $query->detach();

            return $this;
        }

        $fileIds = [];

        foreach ($files as $file) {
            $fileIds[] = $file instanceof File ? $file->id : $file;
        }

        if ($fileIds) {
            $query->detach($fileIds);
        }

        return $this;
    }
}

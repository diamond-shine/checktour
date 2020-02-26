<?php

namespace Ideil\LaravelFileManager\Resolver;

use Ideil\LaravelFileManager\Models\File;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

class BaseResolver
{
    /**
     * @var string
     */
    protected $hashAlphabet = 'abcdefghijklmnopqrstuvwxyz234567';

    /**
     * @var int
     */
    protected $hashAlphabetBitsPerChar = 5;

    /**
     * @var FilesystemAdapter
     */
    protected $fileSystem;

    /**
     * @var array
     */
    protected $diskInfo;

    /**
     * BaseResolver constructor.
     * @param FilesystemAdapter $fileSystem
     * @param array $diskInfo
     */
    public function __construct(FilesystemAdapter $fileSystem, array $diskInfo)
    {
        $this->fileSystem = $fileSystem;
        $this->diskInfo = $diskInfo;
    }

    /**
     * @param string|null $query
     * @param null $default
     * @return array|mixed
     */
    public function getDiskInfo(string $query = null, $default = null)
    {
        if ($query === null) {
            return $this->diskInfo;
        }

        return Arr::get($this->diskInfo, $query, $default);
    }

    /**
     * @param File $file
     * @return string
     */
    public function makeUrl(File $file): string
    {
        $basePath = $this->makePath($file);

        return sprintf(
            '%s/%s',
            $this->diskInfo['config']['static_url'] ?? '',
            $basePath
        );
    }

    /**
     * @param File $file
     * @return string
     */
    public function makeName(File $file): string
    {
        return $file->hash . '.' . $file->ext;
    }

    /**
     * @param File $file
     * @param string $resize
     * @param array $payload
     * @return string
     */
    public function makeThumbUrl(File $file, string $resize = 'x', array $payload = []): string
    {
        $fileName = $this->makeName($file);

        $signatureQuery = sprintf(
            '/%s/%s/%s/{{conversion}}%s',
            $resize,
            substr($file->hash, -1, 1),
            substr($file->hash, -3, 2),
            $fileName
        );

        if ($conversionQuery = $this->serializeConversion($file, $payload)) {
            $signatureQuery = str_replace(
                '{{conversion}}',
                "{$conversionQuery}-",
                $signatureQuery
            );
        } else {
            $signatureQuery = str_replace(
                '{{conversion}}',
                '',
                $signatureQuery
            );
        }

        $signature = $this->generateSignature($signatureQuery);

        $url = sprintf(
            '%s/thumbs/%s/%s/%s/%s-{{conversion}}%s',
            $this->diskInfo['config']['static_url'] ?? '',
            $resize,
            substr($file->hash, -1, 1),
            substr($file->hash, -3, 2),
            $signature,
            $fileName
        );

        if ($conversionQuery) {
            return str_replace('{{conversion}}', "{$conversionQuery}-", $url);
        }

        return str_replace('{{conversion}}', '', $url);
    }

    /**
     * @param File $file
     * @param array $payload
     * @return string
     */
    protected function serializeConversion(File $file, array $payload): string
    {
        $result = [];

        foreach ($payload as $key => $conversion) {
            if ($conversion === 'fit' || $conversion === 'f') {
                $result[] = 'f';

                continue;
            }

            if (
                (
                    $key === 'background_color'
                    || $key === 'bc'
                )
                && (
                    \preg_match('/^[0-9a-f]{6}$/', $conversion)
                    || $conversion === 'none'
                )
            ) {
                $result[] = "bc{$conversion}";

                continue;
            }

            if ($conversion === 'watermark' || $conversion === 'w') {
                $result[] = 'w';

                continue;
            }

            if (
                (
                    $key === 'watermark'
                    || $key === 'w'
                ) && \is_string($conversion)
            ) {
                $result[] = "w{$conversion}";
            }

            if ($conversion === 'crop' || $conversion === 'c') {
                $result[] = 'c';

                continue;
            }

            if ($conversion === 'crop-postpone' || $conversion === 'cp') {
                $result[] = 'cp';

                continue;
            }

            if (
                (
                    $key === 'crop'
                    || $key === 'c'
                ) && \preg_match('/^[p]?(\d+x\d+|\*x\*){1}(x\d+(\.\d+)?[p]?){0,2}$/', $conversion)
            ) {
                $result[] = "c{$conversion}";
            }
        }

        $query = \implode('-', $result);

        return $query ? "{$query}-" : '';
    }

    /**
     * @param File $file
     * @param bool $full
     * @return string
     */
    public function makePath(File $file, bool $full = false): string
    {
        $basePath = sprintf(
            'files/%s/%s',
            substr($file->hash, -1, 1),
            substr($file->hash, -3, 2)
        );

        $path = $basePath . '/' . $this->makeName($file);

        if (! $full) {
            return $path;
        }

        return "{$this->diskInfo['root']}/{$path}";
    }

    /**
     * @param File $file
     * @param UploadedFile $uploadedFile
     * @return bool
     */
    public function putFile(File $file, UploadedFile $uploadedFile): bool
    {
        $pathInfo = \pathinfo(
            $this->makePath($file)
        );

        return (bool)$this->fileSystem->putFileAs(
            $pathInfo['dirname'],
            $uploadedFile,
            $pathInfo['basename']
        );
    }

    /**
     * @param string $query
     * @return string
     * @throws \InvalidArgumentException
     */
    public function generateSignature(string $query): string
    {
        $base = new \Base2n(
            $this->hashAlphabetBitsPerChar,
            $this->hashAlphabet,
            true,
            true,
            true
        );

        $token = $this->getDiskInfo('config.token');

        $encoded = $base->encode(
            \hash(
                'sha256',
                $token . $query,
                true
            )
        );

        return \substr(
            \substr($encoded, 0, 32),
            0,
            6
        );
    }
}

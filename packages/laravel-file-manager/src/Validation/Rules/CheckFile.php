<?php

namespace Ideil\LaravelFileManager\Validation\Rules;

use Closure;
use Ideil\LaravelFileManager\Models\File;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CheckFile implements Rule
{
    /**
     * @var FormRequest|null
     */
    protected $request;

    /**
     * @var array
     */
    protected $criteria;

    /**
     * @var array
     */
    protected $error;

    /**
     * CheckFile constructor.
     * @param FormRequest $request
     */
    public function __construct(FormRequest $request = null)
    {
        $this->request = $request;

        $this->criteria = [];
    }


    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $file = File::find($value);

        if (! $file) {
            $this->error = [
                'type' => 'not_exist',
            ];

            return false;
        }

        foreach ($this->criteria as $rule) {
            $status = $this->{'check' . Str::ucfirst($rule['type']) . 'Rule'}(
                $file,
                $rule['payload'] ?? null,
                $attribute,
                $value
            );

            if (! $status) {
                $this->error['attribute'] = $attribute;
                $this->error['value'] = $value;

                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if ($this->error['type'] === 'custom') {
            return $this->error['message'];
        }

        $messages = null;

        if ($this->request) {
            $messages = $this->request->messages();
        }

        $message = $messages["{$this->error['attribute']}.check_file_{$this->error['type']}"] ??
            trans("validation.check_file_{$this->error['type']}");

        [$keys, $values] = Arr::divide($this->error['payload'] ?? []);

        $keys = \array_map(function (string $key) {
            return ":{$key}";
        }, $keys);

        return \str_replace($keys, $values, $message);
    }

    /**
     * @return $this
     */
    public function image(): self
    {
        $this->criteria['image'] = [
            'type' => 'image',
        ];

        return $this;
    }

    /**
     * @param string $mime
     * @return $this
     */
    public function mime($mime): self
    {
        $mime = ! \is_array($mime) ? [$mime] : $mime;

        $payload = [];

        foreach ($mime as $item) {
            $parts = \explode('/', $item);

            if (\count($parts) === 1) {
                $payload[] = $parts[0];
            } else {
                $payload[] = $parts;
            }
        }

        $this->criteria['mime'] = [
            'type' => 'mime',
            'payload' => $payload,
        ];

        return $this;
    }

    /**
     * @param int $value
     * @param string $operator
     * @return $this
     */
    public function width(int $value, string $operator = '='): self
    {
        $this->criteria[] = [
            'type' => 'width',
            'payload' => [
                'value' => $value,
                'operator' => $operator,
            ],
        ];

        return $this;
    }

    /**
     * @param int $value
     * @param string $operator
     * @return $this
     */
    public function height(int $value, string $operator = '='): self
    {
        $this->criteria[] = [
            'type' => 'height',
            'payload' => [
                'value' => $value,
                'operator' => $operator,
            ],
        ];

        return $this;
    }

    /**
     * @param int $value
     * @param string $operator
     * @return $this
     */
    public function size(int $value, string $operator = '='): self
    {
        $this->criteria[] = [
            'type' => 'size',
            'payload' => [
                'value' => $value,
                'operator' => $operator,
            ],
        ];

        return $this;
    }

    /**
     * @param Closure $callback
     * @return $this
     */
    public function where(Closure $callback): self
    {
        $this->criteria[] = [
            'type' => 'where',
            'payload' => $callback,
        ];

        return $this;
    }

    /**
     * @param File $file
     * @return bool
     */
    protected function checkImageRule(File $file): bool
    {
        $status = $file->isImage();

        if (! $status) {
            $this->error = [
                'type' => 'image',
            ];
        }

        return $status;
    }

    /**
     * @param File $file
     * @param string|array $mime
     * @return bool
     */
    protected function checkMimeRule(File $file, array $mime): bool
    {
        foreach ($mime as $item) {
            $status = \is_string($item) ?
                Str::startsWith($file->mime, "{$item}/") :
                $file->mime === \implode('/', $item);

            if (! $status) {
                $this->error = [
                    'type' => 'mime',
                    'payload' => [
                        'mime' => \implode(', ', $mime),
                    ],
                ];
            }
        }

        return true;
    }

    /**
     * @param File $file
     * @param array $payload
     * @return bool
     */
    protected function checkWidthRule(File $file, array $payload): bool
    {
        $status = $this->compare(
            $file->width,
            $payload['operator'],
            $payload['value']
        );

        if (! $status) {
            $this->error = [
                'type' => 'width_' . $this->compareType($payload['operator']),
                'payload' => [
                    'width' => $payload['value'],
                ],
            ];
        }

        return $status;
    }

    /**
     * @param File $file
     * @param array $payload
     * @return bool
     */
    protected function checkHeightRule(File $file, array $payload): bool
    {
        $status = $this->compare(
            $file->height,
            $payload['operator'],
            $payload['value']
        );

        if (! $status) {
            $this->error = [
                'type' => 'height_' . $this->compareType($payload['operator']),
                'payload' => [
                    'height' => $payload['value'],
                ]
            ];
        }

        return $status;
    }

    /**
     * @param File $file
     * @param array $payload
     * @return bool
     */
    protected function checkSizeRule(File $file, array $payload): bool
    {
        $size = $file->size;

        if (Str::endsWith($size, 'K')) {
            $size = Str::replaceLast('K', '000', $size);
        }

        if (Str::endsWith($size, 'M')) {
            $size = Str::replaceLast('M', '000000000', $size);
        }

        if (Str::endsWith($size, 'G')) {
            $size = Str::replaceLast('G', '000000000000', $size);
        }

        $status = $this->compare($size, $payload['operator'], $payload['value']);

        if (! $status) {
            $this->error = [
                'type' => 'size_' . $this->compareType($payload['operator']),
                'payload' => [
                    'size' => $payload['value'],
                ]
            ];
        }

        return $status;
    }

    /**
     * @param File $file
     * @param Closure $callback
     * @param string $attribute
     * @param string $value
     * @return bool
     */
    protected function checkWhereRule(File $file, Closure $callback, string $attribute, string $value): bool
    {
        $message = null;

        $callback(
            $attribute,
            $value,
            function (string $text) use (&$message) {
                $message = $text;
            }
        );

        if ($message !== null) {
            $this->error = [
                'type' => 'custom',
                'message' => $message,
            ];

            return false;
        }

        return true;
    }

    /**
     * @param float $a
     * @param string $operator
     * @param float $b
     * @return bool
     */
    protected function compare(float $a, string $operator, float $b): bool
    {
        switch ($operator) {
            case '=':
                return $a === $b;

            case '>':
                return $a > $b;

            case '<':
                return $a < $b;

            case '>=':
                return $a >= $b;

            case '<=':
                return $a <= $b;
        }

        throw new InvalidArgumentException("Not allowed operator [{$operator}]. Allowed: =, >, <, >=, <=");
    }

    /**
     * @param string $operator
     * @return string
     */
    protected function compareType(string $operator): string
    {
        switch ($operator) {
            case '=':
                return 'equal';

            case '>':
                return 'gt';

            case '<':
                return 'lt';

            case '>=':
                return 'gle';

            case '<=':
                return 'lte';
        }

        throw new InvalidArgumentException("Not allowed operator [{$operator}]. Allowed: =, >, <, >=, <=");
    }

    /**
     * @param FormRequest|null $request
     * @return CheckFile
     */
    public static function make(FormRequest $request = null): CheckFile
    {
        return new self($request);
    }
}

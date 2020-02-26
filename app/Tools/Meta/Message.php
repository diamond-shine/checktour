<?php

namespace App\Tools\Meta;

class Message extends AbstractMeta
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $type;

    /**
     * @param string|null $text
     */
    public function __construct(string $text = null)
    {
        $this->message = $text;

        $this->type = 'info';
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return 'message';
    }

    /**
     * @return self
     */
    public function success(): self
    {
        $this->type = 'success';

        return $this;
    }

    /**
     * @return self
     */
    public function warning(): self
    {
        $this->type = 'warning';

        return $this;
    }

    /**
     * @return self
     */
    public function info(): self
    {
        $this->type = 'info';

        return $this;
    }

    /**
     * @return self
     */
    public function error(): self
    {
        $this->type = 'error';

        return $this;
    }

    /**
     * @return array
     */
    public function payload(): array
    {
        return [
            'type' => $this->type,
            'message' => $this->message,
        ];
    }
}

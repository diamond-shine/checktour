<?php

namespace Shelter\Kernel\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;

class AbstractCommand extends Command
{
    /**
     * @param string $name
     * @param string $question
     * @param $rules
     * @param array $messages
     * @param null $default
     * @param string $type
     * @return string
     */
    protected function askAndValidate(
        string $name,
        string $question,
        $rules,
        array $messages = [],
        $default = null,
        string $type = 'ask'
    ): string {
        $isValid = false;

        if (\is_string($rules)) {
            $rules = explode('|', $rules);
        }

        array_unshift($rules, 'required');

        $formattedMessages = collect($messages)
            ->mapWithKeys(function (string $value, string $key) use ($name) {
                return [
                    "{$name}.{$key}" => $value,
                ];
            })
            ->toArray();

        do {
            $answer = $this->{$type}($question, $default);

            $validator = \Validator::make(
                [
                    $name => $answer,
                ],
                [
                    $name => $rules,
                ],
                $formattedMessages
            );

            if ($validator->passes()) {
                $isValid = true;
            } else {
                foreach ($validator->errors()->get($name) as $error) {
                    $this->error("- {$error}");
                }
            }

        } while (! $isValid);

        return $answer;
    }

    /**
     * @param int $max
     * @return ProgressBar
     */
    public function createProgressBar(int $max): ProgressBar
    {
        return $this->getOutput()->createProgressBar($max);
    }
}

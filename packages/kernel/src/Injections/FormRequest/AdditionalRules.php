<?php

namespace Shelter\Kernel\Injections\FormRequest;

use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Shelter\Kernel\Http\FormRequestMixinInterface;

/**
 * Trait AdditionalRules
 * @package Shelter\Kernel\Injections\FormRequest
 */
trait AdditionalRules
{
    /**
     * @param ValidationFactory $factory
     * @return Validator
     */
    protected function createDefaultValidator(ValidationFactory $factory): Validator
    {
        $rules = [];
        $messages = [];

        foreach ($this->mixins ?? [] as $injectionClass) {
            /** @var FormRequest $injection */
            $injection = new $injectionClass;

            if (! $injection instanceof FormRequestMixinInterface) {
                throw new \RuntimeException(
                    "[{$injection}] must implement [" . FormRequestMixinInterface::class . ']'
                );
            }

            $mixinRules = \method_exists($injection, 'rules') ?
                $injection->rules($this) :
                [];

            if ($mixinRules) {
                $rules[] = $mixinRules;
            }

            $mixinMessages = \method_exists($injection, 'messages') ?
                $injection->messages($this) :
                [];

            if ($mixinMessages) {
                $messages[] = $mixinMessages;
            }
        }

        if ($rules) {
            $rules = \array_merge(...$rules);
        }

        if ($messages) {
            $messages = \array_merge(...$messages);
        }

        return $factory->make(
            $this->validationData(),
            \array_merge(
                $this->container->call([$this, 'rules']),
                $rules
            ),
            \array_merge(
                $messages,
                $this->messages()
            ),
            $this->attributes()
        );
    }
}

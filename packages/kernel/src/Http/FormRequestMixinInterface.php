<?php

namespace Shelter\Kernel\Http;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Interface FormRequestMixinInterface
 * @package Shelter\Kernel\Http
 */
interface FormRequestMixinInterface
{
    /**
     * @param FormRequest $request
     * @return array
     */
    public function rules(FormRequest $request): array;
}

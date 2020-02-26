<?php

namespace Shelter\Kernel\Http;

use Illuminate\Foundation\Http\FormRequest;
use Shelter\Kernel\Injections\FormRequest\AdditionalRules;

abstract class AbstractFormRequest extends FormRequest
{
    use AdditionalRules;

    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }
}

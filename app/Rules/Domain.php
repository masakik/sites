<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Domain implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (config('sites.subdominio')) {
            $pattern = "/^((?!-)[A-Za-z0-9-.]{1,63}(?<!-))$/";
        } else {
            $pattern = "/^((?!-)[A-Za-z0-9-]{1,63}(?<!-))$/";
        }

        return preg_match($pattern, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Domínio inválido';
    }
}

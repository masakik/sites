<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Domain implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // remove todos caracteres ilegais da url
        $url = filter_var($value, FILTER_SANITIZE_URL);
        // adiciona protocolo e sufixo do domínio
        $url = 'http://' . $url . env('DNSZONE');
        // valida url
        if (filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O domínio não é um formato válido.';
    }
}

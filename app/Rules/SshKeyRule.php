<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SshKeyRule implements Rule
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
        $pattern = '/^ssh-(rsa|dsa|ed25519|ecdsa)\s+[A-Za-z0-9+\/]+[=]{0,3}(\s+[^\s]+)?$/';
        return preg_match($pattern, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('api.user.ssh_key');
    }
}

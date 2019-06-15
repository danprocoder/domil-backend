<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class UploadedImageUrl implements Rule
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
        if (!$value) {
            return false;
        } elseif (!preg_match('~^https:\/\/cloud\.domil\.com\/uploads\/portfolio\/[a-z0-9]+\.(jpg|png|jpeg)$~', $value)) {
            return false;
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
        return 'The image URL is not valid';
    }
}

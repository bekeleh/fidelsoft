<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class DateRule implements Rule
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
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //

        return false;
    }

    public function before()
    {
        return function (Carbon $date) {
            return 'before:' . $date->toDateTimeString();
        };
    }

    public function beforeOrEqual()
    {
        return function (Carbon $date) {
            return 'before_or_equal:' . $date->toDateTimeString();
        };
    }

    public function after()
    {
        return function (Carbon $date) {
            return 'after:' . $date->toDateTimeString();
        };
    }

    public function afterOrEqual()
    {
        return function (Carbon $date) {
            return 'after_or_equal:' . $date->toDateTimeString();
        };
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}

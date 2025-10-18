<?php

namespace App\Actions\Fortify;

use Illuminate\Validation\Rules\Password;

trait PasswordValidationRules
{
    protected function passwordRules()
    {
        return ['required', 'string', Password::default(), 'confirmed'];
    }
}

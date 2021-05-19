<?php

namespace App\RequestRules\User;

use App\RequestRules\IRequestRules;

class UpdateUserRequest implements IRequestRules
{
    public static function rules(): array
    {
        return [
           'name' => 'required',
           'email' => 'required|email',
           'phone' => 'nullable',
           'address' => 'nullable'
       ];
    }

    public static function messages(): array
    {
        // TODO: Implement messages() method.
    }
}

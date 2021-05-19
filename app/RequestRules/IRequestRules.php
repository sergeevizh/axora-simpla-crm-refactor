<?php

namespace App\RequestRules;

interface IRequestRules
{
    public static function rules(): array;

    public static function messages(): array;
}

<?php

declare(strict_types=1);

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255|unique:clients,name',
            'email'    => 'required|email|unique:clients,email',
            'password' => 'required|string|max:255',
        ];
    }
}

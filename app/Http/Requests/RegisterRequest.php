<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'     => ['bail', 'required', 'string', 'max:255'],
            'email'    => ['bail', 'required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['bail', 'required', 'confirmed', Password::defaults()],
        ];
    }

    public function validated($key = null, $default = null)
    {
        if ($this->has('password')) {
            return array_merge(parent::validated(), ['password' => Hash::make($this->input('password'))]);
        }

        return parent::validated($key, $default); // TODO: Change the autogenerated stub
    }
}

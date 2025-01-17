<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
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
            'username' => 'required|string|max:255|min:3|exists:users,username', 
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'O campo usuário é obrigatório.',
            'username.string' => 'O usuário deve ser um texto válido.',
            'username.max' => 'O usuário não pode exceder 255 caracteres.',
            'username.min' => 'O usuário ter no mínimo 3 caracteres.',
            'username.exists' => 'O usuário informado não existe.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.string' => 'A senha deve ser um texto válido.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.regex' => 'A senha deve conter pelo menos 8 caracteres, incluindo uma letra maiúscula, uma letra minúscula, um número e um símbolo (@$!%*?&).',
        ];
    }

    protected function failedValidation(Validator $validator)
{
    throw new HttpResponseException(response()->json([
        'status' => 'error',
        'message' => 'Username ou senha inválidos.',
        'errors' => $validator->errors(),
    ], 422));
}

}

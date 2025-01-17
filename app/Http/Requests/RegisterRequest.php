<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|min:3|unique:users,username', 
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O nome deve ser um texto válido.',
            'name.max' => 'O nome não pode exceder 255 caracteres.',
            'username.required' => 'O campo usuário é obrigatório.',
            'username.string' => 'O usuário deve ser um texto válido.',
            'username.max' => 'O usuário não pode exceder 255 caracteres.',
            'username.min' => 'O usuário informado já está em uso.',
            'username.unique' => 'O usuário informado já existe.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.string' => 'A senha deve ser um texto válido.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.regex' => 'A senha deve conter pelo menos 8 caracteres, incluindo uma letra maiúscula, uma letra minúscula, um número e um símbolo (@$!%*?&).',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'O email deve ser válido.',
            'email.max' => 'O email não pode exceder 255 caracteres.',
            'email.unique' => 'O email informado já está em uso.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Verifique os campos e tente novamente.',
            'errors' => $validator->errors(),
        ], 422));
    }
}

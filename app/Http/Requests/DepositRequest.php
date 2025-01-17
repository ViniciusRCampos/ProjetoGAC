<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
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
            'amount' => 'required|numeric|min:0.01', 
            'actionId' => 'required|exists:operation_actions,id',
        ];
    }

    /**
     * Mensagens customizadas de validação (opcional).
     *
     * @return array
     */
    public function messages()
    {
        return [
            'amount.required' => 'O valor da transferência é obrigatório.',
            'amount.numeric' => 'O valor deve ser numérico.',
            'amount.min' => 'O valor da transferência deve ser maior que zero.',
            'actionId.required' => 'A ação é obrigatório.',
            'actionId.exists' => 'A ação selecionada não existe.',
        ];
    }
}

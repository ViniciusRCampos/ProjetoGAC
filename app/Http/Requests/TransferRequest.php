<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Aqui você pode definir lógica de autorização, retornando true para permitir a requisição
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'accountNumber' => 'required|regex:/^\d{10}$/', 
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
            'accountNumber.required' => 'O número da conta é obrigatório.',
            'accountNumber.regex' => 'A conta é composta por apenas números e possui 10 digitos.',
            'amount.required' => 'O valor da transferência é obrigatório.',
            'amount.numeric' => 'O valor deve ser numérico.',
            'amount.min' => 'O valor da transferência deve ser maior que zero.',
            'actionId.required' => 'A ação é obrigatório.',
            'actionId.exists' => 'A ação selecionada não existe.',
        ];
    }
}
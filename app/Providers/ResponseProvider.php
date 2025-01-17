<?php

namespace App\Providers;

class ResponseProvider
{
    public function success($data, $message = "Success", $statusCode = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    public function error($message, $statusCode = 400)
    {

        $errorMessages = [
            400 => 'Requisição inválida.',
            404 => 'Recurso não encontrado.',
            422 => 'Erro de validação.',
            500 => 'Erro interno no servidor.',
        ];

        $defaultMessage = $errorMessages[$statusCode] ?? 'Ocorreu um erro inesperado. Tente novamente mais tarde.';

        return response()->json([
            'status' => 'error',
            'message' => $message ?: $defaultMessage,
            'data' => null
        ], $statusCode ?: 500);
    }
}

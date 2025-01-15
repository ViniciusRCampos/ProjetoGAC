<?php

namespace App\Http\Services;

use App\Models\Account;
use App\Models\BalanceLog;
use App\Models\OperationAction;
use App\Models\OperationLog;
use App\Providers\ResponseProvider;
use Illuminate\Http\JsonResponse;

class BalanceLogService
{

    private $responseProvider;
    public function __construct(ResponseProvider $responseProvider)
    {
        $this->responseProvider = $responseProvider;
    }

    private function generateDescription(OperationLog $operationLog): array
    {
        $actionName = OperationAction::find($operationLog['action_id'])->name;
        $toAccount = Account::with('user')->find($operationLog['to_account_id']);
        $fromAccount = Account::with('user')->find($operationLog['from_account_id']);

        switch ($actionName) {
            case 'transfer':

                return [
                    'to' => "Transferencia para {$toAccount->user->name}",
                    'from' => "Transferencia de {$fromAccount->user->name}"
                ];

            case 'reversal-transfer':

                return [
                    'to' => "Estorno da transferencia para {$toAccount->user->name}",
                    'from' => "Estorno da transferencia de {$fromAccount->user->name}"
                ];

            case "reversal":

                return [
                    'to' => "Estorno do depósito numéro {$operationLog['origin_operation_id']}"
                ];

            case 'deposit':

                return [
                    'to' => "Depósito realizado"
                ];

            default:
                throw new \InvalidArgumentException("Action not supported");
        }
    }

    public function createNewBalanceLog(OperationLog $operationLog): JsonResponse
    {
        try {
            $description = $this->generateDescription($operationLog);

            $amount = isset($operationLog['origin_operation_id'])
                ? ($operationLog['amount'] * -1)
                : $operationLog['amount'];

            $amounts = [
                'to' => $amount,
                'from' => $amount * -1,
            ];

            $log = [];
            foreach ($description as $key => $value) {
                $log[] = BalanceLog::createLog([
                    'amount' => $amounts[$key],
                    'description' => $description[$key],
                    'account_id' => $operationLog[$key . '_account_id'],
                    'operation_id' => $operationLog['id'],
                ]);
            }
            return $this->responseProvider->success($log, 'Balance log created', 201);
        } catch (\Exception $e) {
            return $this->responseProvider->error($e->getMessage(), 500);
        }
    }
}

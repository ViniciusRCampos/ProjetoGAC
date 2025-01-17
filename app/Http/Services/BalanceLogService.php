<?php

namespace App\Http\Services;

use App\Models\Account;
use App\Models\BalanceLog;
use App\Models\OperationAction;
use App\Models\OperationLog;
use App\Models\User;
use App\Providers\ResponseProvider;
use Illuminate\Http\JsonResponse;

class BalanceLogService
{

    private $responseProvider;
    public function __construct(ResponseProvider $responseProvider)
    {
        $this->responseProvider = $responseProvider;
    }
    /**
     * Generates default text for descriptions of operations performed
     * @param \App\Models\OperationLog $operationLog
     * @return array
     */
    private function generateDescription(OperationLog $operationLog): array
    {
        $actionName = OperationAction::find($operationLog['action_id'])->name;
        $toAccount = Account::with('user')->find($operationLog['to_account_id']);
        $fromAccount = Account::with('user')->find($operationLog['from_account_id']);
        if(isset($operationLog->origin_operation_id)){
            if(isset($fromAccount)){
                $fromAccountOriginalDescription = strtolower(BalanceLog::where('operation_id', $operationLog->origin_operation_id)
                ->where('account_id', $fromAccount->id )
                ->first()
                ->description);
            }
            $toAccountOriginalDescription = strtolower(BalanceLog::where('operation_id', $operationLog->origin_operation_id)
            ->where('account_id', $toAccount->id )
            ->first()
            ->description);
        }


        switch (strtolower($actionName)) {
            case 'transferir':

              return [
                    'to' => "Transferencia de {$toAccount->user->name}",
                    'from' => "Transferencia para {$fromAccount->user->name}"
                ];
                break;
            case 'estornar':

              return [
                    'to' => "Estorno da operação: {$toAccountOriginalDescription}",
                    'from' => isset($fromAccount) ? "Estorno da operação:  {$fromAccountOriginalDescription}" : null
                ];
                break;
                
            case 'depositar':

              return [
                    'to' => "Depósito realizado"
                ];
                break;
        }
    }
    /**
     * Creates a new record in the balanceLog table with the received data
     * @param \App\Models\OperationLog $operationLog
     * @return \Illuminate\Http\JsonResponse
     */
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
                if($value){
                    $log[] = BalanceLog::createLog([
                        'amount' => $amounts[$key],
                        'description' => $description[$key],
                        'account_id' => $operationLog[$key . '_account_id'],
                        'operation_id' => $operationLog['id'],
                        'processed_at' => $operationLog['fulfilled'] ? now() : null,
                    ]);
                }
            }
            return $this->responseProvider->success($log, 'Balance log created', 201);
        } catch (\Exception $e) {
            return $this->responseProvider->error($e->getMessage(), 500);
        }
    }

    /**
     * Search for all records in the balanceLog table by the account ID
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllBalanceLogByAccountId(User $user): JsonResponse
    {
        try {
            $balanceLogs = BalanceLog::getHistory($user->account->id);
            return $this->responseProvider->success($balanceLogs, 'Balance logs retrieved', 200);
        } catch (\Exception $e) {
            return $this->responseProvider->error($e->getMessage(), 500);
        }
    }
}

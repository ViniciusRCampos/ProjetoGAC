<?php

namespace App\Http\Services;

use App\Models\Account;
use App\Models\OperationLog;
use App\Providers\ResponseProvider;
use App\Http\Services\OperationLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class FinancialService
{
    private $responseProvider;
    public function __construct(ResponseProvider $responseProvider)
    {
        $this->responseProvider = $responseProvider;
    }

    public function processPendingOperation(OperationLog $operationLog)
    {
        $account = Account::find($operationLog->to_account_id);
        try {

            DB::transaction(function () use ($account, $operationLog) {
                $account->increment('balance', $operationLog->amount);
                $operationLog->fulfilled = true;
                $operationLog->fulfilled_at = now();
                $operationLog->save();
                $account->save();
            });
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['operation_id' => $operationLog->id]);
        }
    }

    public function transfer(Request $request): OperationLog
    {
        $fromAccount = Account::find($request->accountId);
        $toAccount = Account::find($request->toAccountNumber);
        $amount = $request->amount;

        if (!$fromAccount->active || !$toAccount->active) {
            throw new Exception('Uma das contas esta inativa. Não é possível realizar a transferência.');
        }

        if ($fromAccount->balance < $amount) {
            throw new Exception('Saldo insuficiente para realizar a transferência.');
        }

        return DB::transaction(function () use ($fromAccount, $amount, $request) {
            $fromAccount->balance -= $amount;
            $fromAccount->save();


            $operationLog = app()->make(OperationLogService::class);
            $operationLog->registerNewOperation($request);

            return $operationLog;
        });
    }

    public function reverse(Request $request): OperationLog
    {
        $originOperationLog = OperationLog::find($request->originOperationId);
        $toAccount = Account::find($originOperationLog->to_account_id);
        
        if ($toAccount->balance < $originOperationLog->amount) {
            throw new Exception('Saldo insuficiente para realizar o estorno.');
        }

        // Validações de saldo para o estorno

        // Realiza estorno
        return DB::transaction(function () use ($originOperationLog, $request) {
            $toAccount = Account::find($originOperationLog->to_account_id);
            $toAccount->balance -= $originOperationLog->amount;
            $toAccount->save();

            if($originOperationLog->from_account_id != null) {
                $fromAccount = Account::find($originOperationLog->from_account_id);
                $fromAccount->balance += $originOperationLog->amount;
                $fromAccount->save();
            }

            // Cria log de estorno
            $reverseLog = OperationLog::create([
                'amount' => $originOperationLog->amount,
                'fulfilled' => $originOperationLog->fulfilled == true ? false : true,
                'fulfilled_at' => $originOperationLog->fulfilled == true ? null : now(),
                'action_id' => $request->actionId, // Código da ação de estorno
                'from_account_id' => $originOperationLog->from_account_id,
                'to_account_id' => $originOperationLog->to_account_id,
                'origin_operation_id' => $originOperationLog->id,
            ]);

            $balanceLogService = app()->make(BalanceLogService::class);
            $balanceLogService->createNewBalanceLog($reverseLog);

            return $reverseLog;
        });
    }
}

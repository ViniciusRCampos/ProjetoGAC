<?php

namespace App\Http\Services;

use App\Models\Account;
use App\Models\OperationLog;
use App\Providers\ResponseProvider;
use App\Http\Services\OperationLogService;
use App\Models\BalanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Http\JsonResponse;

class FinancialService
{
    private $responseProvider;
    public function __construct(ResponseProvider $responseProvider)
    {
        $this->responseProvider = $responseProvider;
    }
    /**
     * Processes data from all pending operations
     * @param \App\Models\OperationLog $operationLog
     * @return void
     */
    public function processPendingOperation(OperationLog $operationLog)
    {
        $account = Account::find($operationLog->to_account_id);
        try {

            DB::transaction(function () use ($account, $operationLog) {
                $account->increment('balance', $operationLog->amount);
                $account->save();

                $operationLog->fulfilled = true;
                $operationLog->fulfilled_at = now();
                $operationLog->save();

                $balanceLog = BalanceLog::where('account_id', $operationLog->to_account_id)->where('operation_id', $operationLog->id)->firstOrFail();
                $balanceLog->processed_at = now();
                $balanceLog->save();

            });
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['operation_id' => $operationLog->id]);
        }
    }
    /**
     * Creates transfer records between accounts
     * @param \Illuminate\Http\Request $request
     * @throws \Exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function transfer(Request $request): JsonResponse
    {
        try {
            $fromAccount = auth()->user()->account;
            $toAccount = Account::where('account_number', $request->toAccountNumber)->firstOrFail();
            $amount = $request->amount;

            $request->merge([
                'toAccountId' => $toAccount->id,
                'fromAccountId' => $fromAccount->id
            ]);

            if (!$fromAccount->active || !$toAccount->active) {
                throw new Exception('Uma das contas esta inativa. Não é possível realizar a transferência.');
            }

            if ($fromAccount->balance < $amount) {
                throw new Exception('Saldo insuficiente para realizar a transferência.');
            }

            $operation =  DB::transaction(function () use ($fromAccount, $amount, $request) {
                $fromAccount->balance -= $amount;
                $fromAccount->save();

                $operationLog = app()->make(OperationLogService::class);
                $operationLog->registerNewOperation($request);

                $balanceLog = BalanceLog::where('account_id', $operationLog->from_account_id)->where('operation_id', $operationLog->id)->firstOrFail();
                $balanceLog->processed_at = now();
                $balanceLog->save();

                return $operationLog;
            });
            return $this->responseProvider->success($operation, 'Success', 201);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$request]);
            return $this->responseProvider->error(null, $e->getCode());
        }
    }
    /**
     * Performs the reversal of an operation and the creation of all the necessary records for the reversal
     * has validations to ensure data integrity and prevent chargebacks without balance
     * @param \Illuminate\Http\Request $request
     * @throws \Exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function reverse(Request $request): JsonResponse
    {
        try {

            $originOperationLog = OperationLog::find($request->originOperationId);
            $toAccount = Account::find($originOperationLog->to_account_id);

            if ($toAccount->balance < $originOperationLog->amount && $originOperationLog->fulfilled) {
                throw new Exception('Saldo insuficiente para realizar o estorno.');
            }

            $operation = DB::transaction(function () use ($originOperationLog, $request) {

                if ($originOperationLog->fulfilled) {
                    $toAccount = Account::find($originOperationLog->to_account_id);
                    $toAccount->balance -= $originOperationLog->amount;
                    $toAccount->save();
                }

                if ($originOperationLog->from_account_id != null) {
                    $fromAccount = Account::find($originOperationLog->from_account_id);
                    $fromAccount->balance += $originOperationLog->amount;
                    $fromAccount->save();
                }

                $reverseLog = OperationLog::create([
                    'amount' => $originOperationLog->amount,
                    'fulfilled' => $originOperationLog->fulfilled == true ? false : true,
                    'fulfilled_at' => $originOperationLog->fulfilled == true ? null : now(),
                    'action_id' => 3, // Código da ação de estorno
                    'from_account_id' => $originOperationLog->from_account_id,
                    'to_account_id' => $originOperationLog->to_account_id,
                    'origin_operation_id' => $originOperationLog->id,
                ]);

                $balanceLogService = app()->make(BalanceLogService::class);
                $balanceLogService->createNewBalanceLog($reverseLog);

                $originOperationLog->fulfilled = true;
                $originOperationLog->fulfilled_at = now();
                $originOperationLog->save();

                return $reverseLog;
            });

            return $this->responseProvider->success($operation, 'Success', 201);
        } catch (Exception $e) {
            Log::error($e->getMessage(), [$request]);
            return $this->responseProvider->error(null, $e->getCode());
        }
    }
}

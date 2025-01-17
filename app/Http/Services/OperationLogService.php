<?php

namespace App\Http\Services;

use App\Models\Account;
use App\Models\BalanceLog;
use App\Models\OperationLog;
use App\Models\User;
use App\Providers\ResponseProvider;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class OperationLogService
{

    private $responseProvider;
    private $transferId = 2;

    public function __construct(ResponseProvider $responseProvider)
    {
        $this->responseProvider = $responseProvider;
    }
    /**
     * Function to prepare data array for new operation log
     * @param \Illuminate\Http\Client\Request $data
     * @return array
     */
    private function prepareNewDataArray(Request $request): array
    {
        $dataArray = [
            'amount' => $request['amount'] ,
            'action_id' => $request['actionId'],
            'to_account_id' => $request['toAccountId'] ?? auth()->user()->account->id,
            'fulfilled' => false,
            'fulfilled_at' => null,
        ];
        if ($request['actionId'] == $this->transferId) {
            $dataArray['from_account_id'] = $request['fromAccountId'] ;
            $toAccount = Account::findAccountId([
                'account_number' => $request['toAccountNumber']
            ]);
            $dataArray['to_account_id'] = $toAccount->id;
        }
        return $dataArray;
    }
    /**
     * Creates a record of a new operation performed
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerNewOperation(Request $request): JsonResponse
    {
        try {
            $data = $this->prepareNewDataArray($request);
            $operationLog = OperationLog::createLog($data);
            
            $balanceLogService = app()->make(BalanceLogService::class);
            $balanceLogService->createNewBalanceLog($operationLog);

            return $this->responseProvider->success($operationLog, 'Operation registered', 201);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['request' => $request->all()]);
            return $this->responseProvider->error($e->getMessage(), $e->getCode() == 404 ? $e->getCode() : 500);
        }
    }

    /**
     * Updates the fields of an already created record
     * @param \App\Models\OperationLog $operationLog
     * @return void
     */
    public function updateOperation(OperationLog $operationLog): void
    {
        try {
            $operationLog->update(['fulfilled' => true, 'fulfilled_at' => now(), 'updated_at' => now()]);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['operation_id' => $operationLog->id]);
        }
    }

    /**
     * Search for all transactions that can still be reversed and list them
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchRefunds(User $user): JsonResponse {
        try{
            $operations = BalanceLog::whereHas('operationLog', function ($query) {
                $query->whereNull('origin_operation_id')
                      ->where(function ($q) {
                          $q->where('fulfilled_at', '<=', now())
                            ->orWhereNull('fulfilled_at');
                      })
                      ->whereNotIn('id', function ($subQuery) {
                          $subQuery->select('origin_operation_id')
                                   ->from('operation_logs')
                                   ->whereNotNull('origin_operation_id');
                      })
                      ->where('to_account_id', auth()->user()->account->id);
            })
            ->where('account_id', auth()->user()->account->id)
            ->get(['id', 'description', 'amount', 'operation_id', 'created_at', 'processed_at']);
            
            return $this->responseProvider->success($operations, 'Refunds fetched', 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['user_id' => $user->id]);
            return $this->responseProvider->error($e->getMessage(), $e->getCode() == 404 ? $e->getCode() : 500);
        }

    }

}

<?php

namespace App\Http\Services;

use App\Models\Account;
use App\Models\OperationLog;
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
    private function prepareNewDataArray(Request $request, OperationLog $operationLog = null): array
    {
        $dataArray = [
            'amount' => $request['amount'] ?? $operationLog->amount,
            'actionId' => $request['actionId'],
            'toAccountId' => $request['toAccountId'] ?? $operationLog->to_account_id,
            'fulfilled' => false,
            'fulfilledAt' => null,
        ];
        if ($request['actionId'] == self::$transferId || $request['actionId'] == self::$$request['actionId'] == self::$transferId) {
            $dataArray['fromAccountId'] = $request['fromAccountId'] ?? $operationLog->from_account_id;
            $toAccount = Account::findAccountId([
                'account_number' => $request['toAccountNumber']
            ]);
            $dataArray['toAccountId'] = $toAccount->id ?? $operationLog->to_account_id;
        }
        return $dataArray;
    }
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

    public function updateOperation(OperationLog $operationLog): void
    {
        try {
            $operationLog->update(['fulfilled' => true, 'fulfilled_at' => now(), 'updated_at' => now()]);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['operation_id' => $operationLog->id]);
        }
    }

}

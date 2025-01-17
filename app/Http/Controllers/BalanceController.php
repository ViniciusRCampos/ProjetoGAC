<?php

namespace App\Http\Controllers;

use App\Http\Services\OperationLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    /**
     * Search for all transactions that can be reversed and list them
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllReversibleTransactions(): JsonResponse
    {
        $user = auth()->user();

        $operationLogService = app()->make(OperationLogService::class);
        $operations = $operationLogService->fetchRefunds($user);

        return $operations;
    }

    /**
     * Fetch the account balance of the logged in user
     * @return JsonResponse|mixed
     */
    public function getBalance(){
        $user = auth()->user();
        return response()->json(['balance' => $user->account->balance], 200);
    }
    
}

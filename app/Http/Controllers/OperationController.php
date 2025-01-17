<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepositRequest;
use App\Http\Requests\TransferRequest;
use App\Http\Services\FinancialService;
use App\Http\Services\OperationLogService;
use App\Models\OperationAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OperationController extends Controller
{
    private $financialService;
    public function __construct(FinancialService $financialService)
    {
        $this->financialService = $financialService;
    }
    /**
     * Renders the operation screen
     * @return mixed|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        if (!auth()->check()) {
            return view("login");
        }

        $actions = OperationAction::all();
        return view('operation')->with('actions', json_decode($actions));
    }
    /**
     * Reverses the selected operation
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refund(Request $request): JsonResponse {
        $refundOperation = $this->financialService->reverse($request);

        return $refundOperation;
    }
    /**
     * Make a balance transfer between two accounts
     * @param \App\Http\Requests\TransferRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transfer(TransferRequest $request): JsonResponse
    {
        $operation = $this->financialService->transfer($request);

        return $operation;
    }

    /**
     * Makes a deposit of a new amount
     * @param \App\Http\Requests\DepositRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deposit(DepositRequest $request): JsonResponse {
        $operationLogService = app()->make(OperationLogService::class);
        $deposit = $operationLogService->registerNewOperation($request);

        return $deposit;
    }


}

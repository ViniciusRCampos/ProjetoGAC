<?php

namespace App\Http\Services;

use App\Models\Account;
use App\Providers\ResponseProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AccountService
{
    private $responseProvider;
    public function __construct(ResponseProvider $responseProvider) {
        $this->responseProvider = $responseProvider;
    }

    public function createNewAccount(Request $request) {
        try {
            $account = Account::createAccount($request->userId, $request->balance);
            return $this->responseProvider->success($account, 'Account created', 201);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['request' => $request->all()]);
            return $this->responseProvider->error(null, $e->getCode());

        }    
    }
    public function getAccount(Request $request) {
        try {
            $account = Account::find($request->accountId);
            return $this->responseProvider->success($account, 'Account found', 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['request' => $request->all()]);
            return $this->responseProvider->error(null, $e->getCode());
        }
    }

    public function toggleAccountStatus(Request $request) {
        try {
            $account = Account::find($request->accountId);
            $account->active = !$account->active;
            $account->save();
            return $this->responseProvider->success($account, 'Account status updated', 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['request' => $request->all()]);
            return $this->responseProvider->error(null, $e->getCode());
        }
    }
}

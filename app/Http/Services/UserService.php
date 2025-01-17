<?php

namespace App\Http\Services;

use App\Models\Account;
use App\Models\User;
use App\Providers\ResponseProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService
{

    private $responseProvider;
    public function __construct(ResponseProvider $responseProvider)
    {
        $this->responseProvider = $responseProvider;
    }
    /**
     * Register a new user and create an account to operate
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try{ 
           $account = DB::transaction(function () use ($request) {
                $user = new User();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->username = $request->username;
                $user->save();

                return Account::createNewAccount($user->id, 0);

            });
            return $this->responseProvider->success($account, 'User created', 201);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['request' => $request->all()]);
            return $this->responseProvider->error(null, $e->getCode());
        }
    }

    public function updateUser(Request $request)
    {
        try {
            $user = Auth::user();
            $updated = false;
            
            if (isset($request->name) && $user->name !== $request->name) {
                $user->name = $request->name;
                $updated = true;
            }
            
            if (isset($request->email) && $user->email !== $request->email) {
                $user->email = $request->email;
                $updated = true;
            }
            
            if (isset($request->password) && !Hash::check($request->password, $user->password)) {
                $user->password = Hash::make($request->password);
                $updated = true;
            }
            
            if ($updated) {
                $user->save();
                return $this->responseProvider->success(['message' => 'Dados atualizados com sucesso'], 200);
            }
            
            return $this->responseProvider->success(['message' => 'Nenhuma alteração foi realizada'], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar dados do usuário', [
                'error' => $e->getMessage(),
                'user_id' => $user->id ?? null,
                'request_data' => $request->all()
            ]);
            
            return $this->responseProvider->error(null, $e->getCode());
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Services\BalanceLogService;
use App\Models\OperationAction;
use Illuminate\Http\Request;

class ExtractController extends Controller
{
    /**
     * Renders the extract screen and fetches the data to populate the table
     * @return \Illuminate\View\View
     */
    public function index(): \Illuminate\View\View
    {
        if (!auth()->check()) {
            return view("login");
        }

        $BalanceLogService = app()->make(BalanceLogService::class);
        $operations = $BalanceLogService->getAllBalanceLogByAccountId(auth()->user())->getData();
        $actions = OperationAction::all();
        return view('extract')->with('operations', $operations->data)->with('actions', json_decode($actions));
    }
}

<?php

namespace App\Jobs;

use App\Http\Services\FinancialService;
use App\Models\OperationLog;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPendingOperations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $operationLog;

    public function __construct(OperationLog $operationLog)
    {
        $this->operationLog = $operationLog;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(FinancialService $financialService)
    {
        try {
            $financialService->processPendingOperation($this->operationLog);
        } catch (Exception $e) {
            logger()->error("Erro ao processar operação pendente: {$e->getMessage()}");
        }
    }
}

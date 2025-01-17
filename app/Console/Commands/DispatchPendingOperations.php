<?php

namespace App\Console\Commands;

use App\Jobs\ProcessPendingOperations;
use App\Models\OperationLog;
use Illuminate\Console\Command;

class DispatchPendingOperations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'operations:dispatch-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Despacha operações pendentes para a fila';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $pendingOperations = OperationLog::where('fulfilled', false)->get();

        foreach ($pendingOperations as $operation) {
            ProcessPendingOperations::dispatch($operation)->onQueue('pending-operations');;
        }

        $this->info(count($pendingOperations) . ' operações pendentes despachadas.');
        return 0;
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * class BalanceLog
 * 
 * @property float $amount
 * @property string $description
 * @property \Carbon\Carbon $processed_at
 * @property int $account_id
 * @property int $operation_id
 * 
 */
class BalanceLog extends Model
{
    use HasFactory;
    protected $table = 'balance_logs';
    protected $fillable = [
        'amount',
        'description',
        'processed_at',
        'account_id',
        'operation_id'
    ];

    protected $casts = [
        'amount'=> 'decimal:2',
        'processed_at'=> 'datetime'
    ];

    /**
     * function to create the relationship between the balance log and the operation log
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function operationLog()
    {
        return $this->belongsTo(OperationLog::class, 'operation_id');
    }


    public static function createLog(array $data): BalanceLog|Model
    {
        return self::create($data);
    }

    public static function updateLog(int $operationId, array $data): BalanceLog|Model
    {
        $log = self::where('operation_id', $operationId)->firstOrFail();
        $log->update($data);
        return $log;
    }

    public static function getHistory(int $accountId): array|Collection
    {
        $history = self::where('account_id', $accountId)->get();

        return $history;
    }
}

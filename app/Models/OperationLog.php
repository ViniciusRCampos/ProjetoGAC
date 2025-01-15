<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationLog extends Model
{
    use HasFactory;

    protected $table = 'operation_logs';

    protected $fillable = [
        'amount',
        'fulfilled',
        'fulfilled_at',
        'action_id',
        'from_account_id',
        'to_account_id'
    ];

    protected $casts = [
        'status_updated_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function actions()
    {
        return $this->belongsTo(OperationAction::class, 'action_id');
    }

    public static function createLog(array $data): OperationLog|Model
    {
        return self::create($data);
    }

}

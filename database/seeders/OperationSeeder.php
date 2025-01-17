<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\BalanceLog;
use App\Models\OperationLog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Run the database seeds.
 *
 * @return void
 */
class OperationSeeder extends Seeder
{
    public function run()
    {
        $user1Account = Account::where('user_id', 1)->first();
        $user2Account = Account::where('user_id', 2)->first();

        // 1. Depósito na conta do usuário 1
        $deposit1 = OperationLog::create([
            'amount' => 1000,
            'fulfilled' => true,
            'fulfilled_at' => now(),
            'action_id' => 1, // ID de "Depositar"
            'from_account_id' => null, // Depósito externo
            'to_account_id' => $user1Account->id,
            'origin_operation_id' => null,
        ]);

        BalanceLog::create([
            'amount' => 1000,
            'description' => 'Depósito inicial',
            'processed_at' => now(),
            'account_id' => $user1Account->id,
            'operation_id' => $deposit1->id,
        ]);

        // 2. Transferência do usuário 1 para o usuário 2
        $transfer = OperationLog::create([
            'amount' => 300,
            'fulfilled' => true,
            'fulfilled_at' => now(),
            'action_id' => 2, // ID de "Transferir"
            'from_account_id' => $user1Account->id,
            'to_account_id' => $user2Account->id,
            'origin_operation_id' => null,
        ]);

        BalanceLog::create([
            'amount' => -300,
            'description' => 'Transferência para usuário 2',
            'processed_at' => now(),
            'account_id' => $user1Account->id,
            'operation_id' => $transfer->id,
        ]);

        BalanceLog::create([
            'amount' => 300,
            'description' => 'Recebido transferência do usuário 1',
            'processed_at' => now(),
            'account_id' => $user2Account->id,
            'operation_id' => $transfer->id,
        ]);

        // 3. Depósito adicional na conta do usuário 1
        $deposit2 = OperationLog::create([
            'amount' => 150,
            'fulfilled' => true,
            'fulfilled_at' => now(),
            'action_id' => 1, // ID de "Depositar"
            'from_account_id' => null,
            'to_account_id' => $user1Account->id,
            'origin_operation_id' => null,
        ]);

        BalanceLog::create([
            'amount' => 150,
            'description' => 'Depósito adicional',
            'processed_at' => now(),
            'account_id' => $user1Account->id,
            'operation_id' => $deposit2->id,
        ]);

        // 4. Estorno do depósito adicional
        $refund = OperationLog::create([
            'amount' => 150,
            'fulfilled' => true,
            'fulfilled_at' => now(),
            'action_id' => 3, // ID de "Estornar"
            'from_account_id' => null,
            'to_account_id' => $user1Account->id,
            'origin_operation_id' => $deposit2->id,
        ]);

        BalanceLog::create([
            'amount' => -150,
            'description' => 'Estorno do depósito adicional',
            'processed_at' => now(),
            'account_id' => $user1Account->id,
            'operation_id' => $refund->id,
        ]);
    }
}

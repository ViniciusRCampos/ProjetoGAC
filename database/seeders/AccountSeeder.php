<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $account1 = Account::create([
            'user_id' => 1, // Relacionamento manual
            'balance' => 0,         // Saldo inicial, por exemplo
        ]);

        $account1->update([
            'account_number' => 1111111111,
            'balance' => 700,
        ]);

        $account2 = Account::create([
            'user_id' => 2, // Relacionamento manual
            'balance' => 0,         // Saldo inicial, por exemplo
        ]);

        $account2->update([
            'account_number' => 2222222222,
            'balance' => 300,
        ]);

    }
}

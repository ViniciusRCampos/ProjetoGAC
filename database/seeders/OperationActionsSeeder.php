<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OperationAction;

class OperationActionsSeeder extends Seeder
{
    public function run()
    {
        $actions = [
            ['name' => 'Depositar', 'description' => 'Adiciona fundos à conta', 'active' => true],
            ['name' => 'Transferir', 'description' => 'Transfere fundos entre contas', 'active' => true],
            ['name' => 'Estornar', 'description' => 'Reverte uma operação anterior', 'active' => true],
        ];

        foreach ($actions as $action) {
            OperationAction::create($action);
        }
    }
}

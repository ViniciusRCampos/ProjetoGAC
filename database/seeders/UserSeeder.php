<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user1 = User::create([
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'password' => bcrypt('Senha@123'),
            'username' => 'teste',
        ]);

        $user2 = User::create([
            'name' => 'Mrs Jhon Doe',
            'email' => 'mrsjohn@doe.com',
            'password' => bcrypt('Senha@123'),
            'username' => 'teste2',
        ]);

    }
}

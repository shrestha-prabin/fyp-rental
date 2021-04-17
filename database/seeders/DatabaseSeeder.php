<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        User::create([
            'id' => '1',
            'name' => 'admin',
            'email' => 'admin@email.com',
            'password' => bcrypt('111111'),
            'role' => 'admin'
        ]);

        User::create([
            'id' => '2',
            'name' => 'seller',
            'email' => 'seller@email.com',
            'password' => bcrypt('111111'),
            'role' => 'seller'
        ]);

        User::create([
            'id' => '3',
            'name' => 'buyer',
            'email' => 'buyer@email.com',
            'password' => bcrypt('111111'),
            'role' => 'buyer'
        ]);
    }
}

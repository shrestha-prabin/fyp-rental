<?php

namespace Database\Seeders;

use App\Models\Apartment;
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

        Apartment::create([
            'seller_id' => '2',
            'name' => 'Office Building',
            'location' => 'Balaju',
            'purpose' => 'Office Space',
            'type' => 'Buy',
            'bhk' => '3',
            'description' => 'Occaecat consequat in et officia ullamco non incididunt ad laborum ad aute Lorem eiusmod.',
            'price' => 1000,
            'image' => ''
        ]);

        Apartment::create([
            'seller_id' => '2',
            'name' => 'Rental Office Building',
            'location' => 'Naxal',
            'purpose' => 'Office Space',
            'type' => 'Rent',
            'bhk' => '3',
            'description' => 'Occaecat consequat in et officia ullamco non incididunt ad laborum ad aute Lorem eiusmod.',
            'price' => 1000,
            'image' => ''
        ]);

        Apartment::create([
            'seller_id' => '2',
            'name' => 'New Office Building',
            'location' => 'Baneshwor',
            'purpose' => 'Office Space',
            'type' => 'Buy',
            'bhk' => '1',
            'description' => 'Occaecat consequat in et officia ullamco non incididunt ad laborum ad aute Lorem eiusmod.',
            'price' => 2000,
            'image' => ''
        ]);
    }
}

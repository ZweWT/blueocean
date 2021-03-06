<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = \App\Models\User::factory()->create([
            'name' => 'blue ocean',
            'email' => 'blueocean@gmail.com',
            'password' => \Illuminate\Support\Facades\Hash::make('blueocean@gmail.com')
        ]);

        $user->assignRole('admin'); 
    }
}

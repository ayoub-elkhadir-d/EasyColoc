<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin EasyColoc',
            'email' => 'admin@easycoloc.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
           
        ]);

        User::create([
            'name' => 'Amine',
            'email' => 'amine@test.com',
            'password' => Hash::make('password'),
            'role' => 'user',
           
        ]);

     
        User::create([
            'name' => 'Sara',
            'email' => 'sara@test.com',
            'password' => Hash::make('password'),
            'role' => 'user',
           
        ]);
    }
}
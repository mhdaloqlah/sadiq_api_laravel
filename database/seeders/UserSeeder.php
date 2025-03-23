<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
       $user = User::create([
        'first_name' => 'Mohammad',
        'last_name' => 'Aloqlah',
        'email' => 'mhd@gmail.com',
        'password'=>Hash::make('admin2024')

       ]);

       $user->createToken('api_token')->plainTextToken;

       $user2 = User::create([
        'first_name' => 'Maria',
        'last_name' => 'Aloqlah',
        'email' => 'maria@gmail.com',
        'password'=>Hash::make('admin2024')

       ]);

       $user2->createToken('api_token')->plainTextToken;

       $user3 = User::create([
        'first_name' => 'Julia',
        'last_name' => 'Aloqlah',
        'email' => 'julia@gmail.com',
        'password'=>Hash::make('admin2024')

       ]);

       $user3->createToken('api_token')->plainTextToken;
        
    }
}

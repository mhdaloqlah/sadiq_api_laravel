<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $service= Service::create([
            'name'=>'teaching students',
            'brief'=>'teaching lessons',
            'category_id'=>1,
            'user_id'=>1
        ]);

        $service2= Service::create([
            'name'=>'medicne ',
            'brief'=>'I am a doctor',
            'category_id'=>1,
            'user_id'=>2
        ]);

        $service3= Service::create([
            'name'=>'programming',
            'brief'=>'teaching programming',
            'category_id'=>1,
            'user_id'=>3
        ]);
    }
}

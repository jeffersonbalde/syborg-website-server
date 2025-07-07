<?php

namespace Database\Seeders;

use App\Models\OwnerUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OwnerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OwnerUser::create([
            'fullname' => 'System Owner',
            'email' => 'owner@system.com',
            'password' => Hash::make('owner123'),
        ]);
    }
}

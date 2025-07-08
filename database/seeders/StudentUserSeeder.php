<?php

namespace Database\Seeders;

use App\Models\StudentUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StudentUser::create([
            'edp_number' => '195367',
            'firstname' => 'Jefferson',
            'middlename' => 'Sabanal',
            'lastname' => 'Balde',
            'course' => 'BSCS - Bachelor of Science in Computer Science',
            'year_level' => '4th Year',
            'status' => 'Irregular Student',
            'gender' => 'Male',
            'age' => 22,
            'birthday' => '2003-05-13',
            'contact_number' => '09513419336',
            'email' => 'jefferson.balde@sccpag.edu.ph',
            'password' => Hash::make('scc195367'),
            'profile_picture' => null,
            'active_status' => 0,
        ]);
    }
}

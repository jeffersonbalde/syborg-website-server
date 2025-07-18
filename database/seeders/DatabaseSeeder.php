<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            ['name' => 'Jefferson Balde', 'password' => bcrypt('admin123')]
        );

        $this->call([
            AdminUserSeeder::class,
            StudentUserSeeder::class,
            OwnerUserSeeder::class,
            HeroSliderSeeder::class,
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\HeroSlider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HeroSliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HeroSlider::create([
            'title' => 'SCC WINS HACK 4 GOV PROVINCIAL',
            'description' => 'description test-1',
            'content' => "content test-1",
        ]);
    }
}

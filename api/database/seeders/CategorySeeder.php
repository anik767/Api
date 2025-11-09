<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Blog',
                'slug' => 'blog',
                'description' => 'All about blog'
            ],
            [
                'name' => 'Tutorial',
                'slug' => 'tutorial',
                'description' => 'All about tutorial'
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}


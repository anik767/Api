<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->info('No users found. Please run UserSeeder first.');
            return;
        }

        // Create posts for each user
        foreach ($users as $user) {
            // Create 3-5 posts per user
            $postCount = 20;
            
            for ($i = 1; $i <= $postCount; $i++) {
                Post::create([
                    'title' => "Post {$i} by {$user->name}",
                    'content' => "This is the content of post {$i} created by {$user->name}. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
                    'image' => 'posts/placeholder.jpg', // Placeholder image
                    'category_id' => 1,
                    'user_id' => $user->id,
                    'published_at' => now(),
                ]);
            }
            
            $this->command->info("Created {$postCount} posts for user: {$user->name}");
        }

        $this->command->info('Posts seeded successfully!');
    }
}



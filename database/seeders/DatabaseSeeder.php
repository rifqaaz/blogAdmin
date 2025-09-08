<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category; 
use App\Models\Post; 
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        
        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('longpassword'),
        ])->assignRole('user');

        User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jj@example.com',
            'password' => bcrypt('123password'),
        ])->assignRole('editor');

        // Create test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ])->assignRole('admin');

        // Seed categories
        $categories = [
            'Technology',
            'Health', 
            'Science',
            'Sports',
            'Politics',
            'Entertainment'
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
                'slug' => strtolower(str_replace(' ', '-', $category))
            ]);
        }

        // Create posts with random category associations
        Post::factory(20)->create();
    }
}
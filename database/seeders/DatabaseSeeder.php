<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Follow;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        {
            // 1. Create 100 users.
            $users = User::factory(10000)->create();
    
            // 2. For each user, create 5 posts.
            foreach ($users as $user) {
                $user->posts()->saveMany(\App\Models\Post::factory(5)->make());
            }
    
            // 3. Randomly make each user follow 10 other users from the created 100 users.
            foreach ($users as $user) {
                $randomUsersToFollow = $users->random(10)->pluck('id')->toArray();
                
                // Ensure the user doesn't follow themselves
                $randomUsersToFollow = array_diff($randomUsersToFollow, [$user->id]);
    
                foreach ($randomUsersToFollow as $followedUserId) {
                    Follow::create([
                        'user_id' => $user->id,
                        'followedUser' => $followedUserId,
                    ]);
                }
            }
        }
    }
}

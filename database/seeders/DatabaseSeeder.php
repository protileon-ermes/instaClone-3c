<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(10)->create();

        $users->each(function ($user) {
            \App\Models\Post::factory(3)->create(['user_id' => $user->id]);
        });

        foreach ($users as $user) {
            $user->following()->attach(
                $users->random(rand(1, 5))->pluck('id')->toArray()
            );
        }

        \App\Models\Comment::factory(50)->create();
        \App\Models\Like::factory(100)->create();
    }
}

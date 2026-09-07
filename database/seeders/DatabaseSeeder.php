<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('AdminPass123!'),
        ]);

        $users = User::factory(9)->create()->push($admin);

        $users->each(function (User $user) use ($users): void {
            Post::factory(rand(2, 4))
                ->for($user)
                ->has(Comment::factory()->count(rand(0, 5)))
                ->recycle($users)
                ->create();
        });
    }
}

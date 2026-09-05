<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Fiksni admin nalog da uvek znaš kako da se uloguješ
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        // 9 običnih korisnika
        $users = User::factory(9)->create();

        // Svaki korisnik (uključujući admina) dobija 2-4 posta, svaki post 0-5 komentara
        $users->push($admin)->each(function (User $user) {
            Post::factory(rand(2, 4))
                ->for($user)
                ->has(\App\Models\Comment::factory()->count(rand(0, 5)))
                ->create();
        });
    }
}
<?php

namespace Database\Seeders;

use App\Enums\UserRoles;
use App\Models\Comment as ModelsComment;
use App\Models\Profile;
use App\Models\Review;
use App\Models\Role;
use App\Models\Tag;
use App\Models\User;
use Database\Factories\ReviewFactory;
use Dom\Comment;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'admin',
        ]);

        Role::create([
            'name' => 'operator',
        ]);

        Role::create([
            'name' => 'client',
        ]);

        Tag::create([
            'name' => 'Reparación',
        ]);

        Tag::create([
            'name' => 'Instalación',
        ]);

        User::factory()->admin()->create([
            'username' => 'admin123',
            'email' => 'admin@example.com'
        ]);

        User::factory()->operator()->create([
            'username' => 'operator123',
            'email' => 'operator@example.com'
        ]);

        User::factory(10)->operator()->create();

        User::factory(20)->create();
    }
}

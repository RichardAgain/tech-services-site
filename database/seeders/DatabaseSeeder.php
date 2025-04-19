<?php

namespace Database\Seeders;

use App\Enums\UserRoles;
use App\Models\Role;
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
        Role::create([
            'name' => 'admin',
        ]);

        Role::create([
            'name' => 'operator',
        ]);

        Role::create([
            'name' => 'client',
        ]);

        User::factory()->admin()->create([
            'username' => 'Admin123',
            'email' => 'admin@example.com'
        ]);

        User::factory(10)->operator()->create();

        User::factory(20)->create();
    }
}

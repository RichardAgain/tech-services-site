<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_see_routes_after_login(): void
    {
        $this->artisan('db:seed');

        $user = User::factory()->create([
            'username' => 'test_user'
        ]);

        $response = $this->post('api/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $token = $response->json('token');

        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->get('api/user');

        // dd($response->json());

        $response->assertStatus(200);
    }

    public function test_can_see_routes_after_register(): void
    {
        $this->artisan('db:seed');

        $data = User::factory()->make()->toArray();

        $response = $this->post('api/register', [
            ...$data,
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $token = $response->json('token');

        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->get('api/user');

        dd($response->json());

        $response->assertStatus(200);
    }
}

<?php

namespace Database\Factories;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => fake()->userName(),
            'firstName' => fake()->firstName(),
            'lastName' => fake()->lastName(),
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role_id' => UserRoles::CLIENT->value,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->role()->associate(UserRoles::ADMIN->value);
            $user->save();
        });
    }

    public function operator(): static
    {
        return $this->has(ProfileFactory::new()->withReviews(), 'profile')
            ->afterCreating(function (User $user) {
                $user->role()->associate(UserRoles::OPERATOR->value);
                $user->profile->tags()->attach(1);
                $user->save();
            });
    }
}

<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
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
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
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

    /**
     * Indicate that the user account is deactivated.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => false,
        ]);
    }

    /**
     * Give the user the Super Admin role (every permission).
     */
    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::firstOrCreate(['slug' => Role::SUPER_ADMIN], ['name' => 'Super Admin', 'permissions' => []])->id,
        ]);
    }

    /**
     * Give the user exactly these permission keys (no role).
     *
     * @param  list<string>  $keys
     */
    public function withPermissions(array $keys): static
    {
        return $this->state(fn (array $attributes) => ['permissions' => $keys]);
    }
}

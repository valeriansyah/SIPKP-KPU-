<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Role;
use App\Models\District;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'full_name' => fake('id_ID')->name(),
            'username' => fake('id_ID')->unique()->userName(),
            'phone_number' => fake('id_ID')->phoneNumber(),
            'email' => fake('id_ID')->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'is_active' => true,
            'role_id' => Role::inRandomOrder()->first()->id ?? 1,
            'district_id' => null,
        ];
    }
}

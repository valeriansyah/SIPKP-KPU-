<?php

namespace Database\Factories;

use App\Models\Deceased;
use App\Models\District;
use App\Models\Report;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeceasedFactory extends Factory
{
    protected $model = Deceased::class;

    public function definition(): array
    {
        $deathDate = fake()->dateTimeBetween('-2 years', 'now');
        $birthDate = fake()->dateTimeBetween('-80 years', '-20 years');

        return [
            'report_id' => Report::factory(),
            'district_id' => District::inRandomOrder()->first()->id ?? 1,
            'nik' => fake()->numerify('1671############'),
            'family_card_number' => fake()->numerify('1671############'),
            'name' => fake('id_ID')->name(),
            'gender' => fake()->randomElement(['Laki-laki', 'Perempuan']),
            'birth_place' => fake('id_ID')->city(),
            'birth_date' => $birthDate,
            'address' => fake('id_ID')->address(),
            'death_place' => fake()->randomElement(['Rumah', 'Rumah Sakit', 'Perjalanan']),
            'death_date' => $deathDate,
            'created_at' => fake()->dateTimeBetween($deathDate, 'now'),
            'updated_at' => function (array $attributes) {
                return fake()->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }
}

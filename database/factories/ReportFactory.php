<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\ReportStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        $today = fake()->dateTimeBetween('-1 year', 'now')->format('Ymd');
        $randomNum = str_pad(fake()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT);

        return [
            'user_id' => User::factory(), // Override later
            'report_status_id' => ReportStatus::inRandomOrder()->first()->id ?? 1,
            'report_number' => 'SIPKP-'.$today.'-'.$randomNum,
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return fake()->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }
}

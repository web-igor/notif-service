<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ReportStatusEnum;
use App\Models\Report;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        $statuses = array_column(ReportStatusEnum::cases(), 'value');

        return [
            'from_date' => now()->subMonth()->format('Y-m-d'),
            'to_date'   => now()->addMonth()->format('Y-m-d'),
            'status'    => fake()->randomElement($statuses),
        ];
    }
}

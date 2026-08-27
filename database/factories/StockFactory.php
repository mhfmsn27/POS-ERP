<?php

namespace Database\Factories;

use App\Models\Product\Stock;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockFactory extends Factory
{
    protected $model = Stock::class;

    public function definition(): array
    {
        return [
            'qty_available' => $this->faker->numberBetween(10, 200),
            'warehouse_id'  => null,
        ];
    }
}

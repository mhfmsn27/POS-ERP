<?php

namespace Database\Factories;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name'           => $this->faker->words(3, true),
            'sku'            => 'PRD-' . strtoupper(Str::random(6)),
            'barcode_type'   => 'C128',
            'alert_quantity' => $this->faker->numberBetween(5, 20),
            'type'           => 'single',
            'is_stock'       => 'yes',
            'is_active'      => 'yes',
            'price_type'     => 'fix',
            'is_unit'        => 'no',
            'is_variant'     => 'no',
            'description'    => $this->faker->sentence(),
            'weight'         => 1,
            'is_account'     => 'no',
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Product\Variation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VariationFactory extends Factory
{
    protected $model = Variation::class;

    public function definition(): array
    {
        $purchasePrice = $this->faker->numberBetween(10000, 100000);
        $sellingPrice  = $purchasePrice + $this->faker->numberBetween(5000, 30000);

        return [
            'name'           => 'default',
            'barcode'        => $this->faker->numerify('899##########'),
            'sku'            => 'VAR-' . strtoupper(Str::random(6)),
            'purchase_price' => $purchasePrice,
            'selling_price'  => $sellingPrice,
            'price_inc_tax'  => 0,
            'tax_sell'       => false,
            'tax_purchase'   => false,
        ];
    }
}

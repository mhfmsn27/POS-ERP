<?php

namespace Database\Factories;

use App\Models\Admin\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoreFactory extends Factory
{
    protected $model = Store::class;

    public function definition(): array
    {
        return [
            'name'           => $this->faker->company() . ' Store',
            'email'          => $this->faker->unique()->companyEmail(),
            'phone'          => $this->faker->phoneNumber(),
            'address'        => $this->faker->address(),
            'tax_one'        => 11,
            'tax_two'        => 0,
            'tax_option'     => 'active',
            'accountant_use' => 'yes',
            'shift_register' => 'active',
        ];
    }
}

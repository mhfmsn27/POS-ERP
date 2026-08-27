<?php

namespace Database\Seeders;

use App\Models\Plugins;
use Illuminate\Database\Seeder;

class EcommercePluginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name'                          => 'E-Commerce',
                'code'                          => 'mdh_ecommerce',
                'plugin_icon'                   => 'fa-globe',
                'status'                        => "1", 
                'plugis_type'                   => 'pay',  
            ]
        ];

        
        Plugins::insert($data);
    }
}

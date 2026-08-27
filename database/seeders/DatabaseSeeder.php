<?php

namespace Database\Seeders;
 
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(CurrencySeeder::class);


        // For Permission Seeder
        $this->call(ModuleFitureSeeder::class);
        $this->call(SettingsSeeder::class);
        $this->call(InventorySeeder::class);
        $this->call(AccountSeeder::class);
        $this->call(MasterDataSeeder::class);
        $this->call(SalesSeeder::class);
        $this->call(PurchaseSeeder::class); 
        $this->call(ReportsSeeder::class);
        $this->call(EcommerceMenuSeeder::class);
        $this->call(EnterpriseMasterSeeder::class);
    }
}

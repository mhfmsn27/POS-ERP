<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Poshub\Ecommerce\Models\EcommerceApiSetting;

class EcommerceSettingSeeder extends Seeder
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
                'facebook_url'                  => 'https://www.facebook.com/poshub',
                'instagram_url'                 => 'https://www.instagram.com/poshub',
                'youtube_url'                   => 'https://www.youtube.com/@poshub',
                'copyright'                     => '© 2026, POSHUB - Omnichannel Enterprise Platform',
                'about_title'                   => 'Tentang Kami',
                'payment_method'                => 'manual',  

            ]
        ];

        EcommerceApiSetting::insert($data);
    }
}

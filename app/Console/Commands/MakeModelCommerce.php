<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MakeModelCommerce extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:makecommerce {name} {--m}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membuat Model Ecommerce Package dan Migrasi database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name       = $this->argument('name');


        Artisan::call('make:model', [
            'name'      => $name,
        ]);

        if ($this->option('m')) {
            Artisan::call('make:migration', [
                'name'      => 'create' . $name . '_table',
                '--path'    => 'packages/poshub/ecommerce/src/database/migrations'
            ]);
        }

        $message = $this->info('Berhasil membuat model ' . $name); 

        if ($this->option('m')) {
            $message = $message . ' ' . $this->info('Berhasil membuat migrasi database ' . $name);
        }

        return $message;
    }
}

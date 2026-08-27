<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigrateCommerce extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:migratecommerce';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrasi Database paket POSHUB ecommerce';

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

        Artisan::call('migrate', [
            '--path' => 'packages/poshub/ecommerce/src/database/migrations'
        ]);

        return $this->info('Migrasi database berhasil dilakukan');
    }
}

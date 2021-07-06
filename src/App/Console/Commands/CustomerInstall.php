<?php

namespace ITHilbert\Customer\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\ComposerScripts;


class CustomerInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installiert alles für Customer (Daten kopieren, Tabellen anlgen, Tabellen befüllen)';

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
        //Customer Dateien
        $this->info('Dateien werden kopiert');
        $this->info('php artisan vendor:publish --provider="ITHilbert\Customer\CustomerServiceProvider"');
        exec('php artisan vendor:publish --provider="ITHilbert\Customer\CustomerServiceProvider"');

        $this->info('php artisan migrate');
        exec('php artisan migrate');

        $this->info('php artisan db:seed --class="ITHilbert\Customer\Database\Seeders\CustomerDatabaseSeeder"');
        exec('php artisan db:seed --class="ITHilbert\Customer\Database\Seeders\CustomerDatabaseSeeder"');

        return 0;
    }
}

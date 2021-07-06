<?php

namespace ITHilbert\Customer\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\ComposerScripts;


class CustomerCopyFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:copyfiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kopiert die Customer Dateien ins Project';

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
        exec('php artisan vendor:publish --provider="ITHilbert\Customer\CustomerServiceProvider" --force');

        return 0;
    }
}

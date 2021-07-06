<?php

namespace ITHilbert\Customer\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Customer\Database\Seeders\CustomerTypesTableSeeder;
use Modules\Customer\Database\Seeders\InvoiceTypesTableSeeder;
use NameAddressTableSeeder;

class CustomerDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Model::unguard();
        $this->call(CustomerTypesTableSeeder::class);
        $this->call(InvoiceTypesTableSeeder::class);
        $this->call(NameAddressTableSeeder::class);
    }
}

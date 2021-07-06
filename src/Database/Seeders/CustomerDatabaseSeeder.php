<?php

namespace ITHilbert\Customer\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use ITHilbert\Customer\Database\Seeders\CustomerTypesTableSeeder;
use ITHilbert\Customer\Database\Seeders\InvoiceTypesTableSeeder;
use ITHilbert\Customer\Database\Seeders\NameAddressTableSeeder;

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

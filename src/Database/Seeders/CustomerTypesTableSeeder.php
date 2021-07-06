<?php
namespace ITHilbert\Customer\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        DB::table('customer_types')->delete();

        DB::table('customer_types')->insert(array (
            0 =>
            array (
                'id' => 1,
                'customer_type' => 'Firma',
            ),
            1 =>
            array (
                'id' => 2,
                'customer_type' => 'Privat',
            ),
        ));


    }
}

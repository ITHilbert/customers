<?php
namespace ITHilbert\Customer\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NameAddressTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        DB::table('name_address')->delete();

        DB::table('name_address')->insert(array (
            0 =>
            array (
                'id' => 1,
                'address' => 'Herr',
            ),
            1 =>
            array (
                'id' => 2,
                'address' => 'Frau',
            ),
        ));


    }
}

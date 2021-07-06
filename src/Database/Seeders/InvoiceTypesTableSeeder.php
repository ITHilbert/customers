<?php
namespace ITHilbert\Customer\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        DB::table('invoice_types')->delete();

        DB::table('invoice_types')->insert(array (
            0 =>
            array (
                'id' => 1,
                'invoice_type' => 'Inlandsgeschäft',
            ),
            1 =>
            array (
                'id' => 2,
                'invoice_type' => 'EU- Reverse Charge',
            ),
            2 =>
            array (
                'id' => 3,
                'invoice_type' => 'Nicht EU Ausland',
            ),
        ));


    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('customer_type_id')->default(1)->comment('1 = Firma (company) 2 = Privat (privat)');
            $table->string('company_row_1',255)->nullable()->comment('For the address');
            $table->string('company_row_2',255)->nullable()->comment('For the address');
            $table->bigInteger('address_id')->default(1)->comment('1 = Herr (Mr) 2 = Frau (Mrs)');
            $table->string('titel',255)->nullable();
            $table->string('first_name',255)->nullable()->comment('Vorname');
            $table->string('last_name',255)->nullable()->comment('Nachname');
            $table->string('street',255)->nullable()->comment('Straße');
            $table->string('postcode',255)->nullable()->comment('PLZ');
            $table->string('city',255)->nullable()->comment('Ort');
            $table->string('country',255)->nullable()->comment('Land');
            $table->string('ustid',255)->nullable()->comment('UmsatzsteuerID');
            $table->string('phone',255)->nullable()->comment('Telefon');
            $table->string('mobile',255)->nullable()->comment('Mobile');
            $table->string('fax',255)->nullable()->comment('Fax');
            $table->string('email',255)->nullable()->comment('Email');
            $table->string('web',255)->nullable()->comment('https://...');
            $table->bigInteger('invoice_type_id')->default(1)->comment('see invoice_typs Table');
            $table->double('hourly_rate', 8, 2)->nullable()->comment('For a new customerload the data from the config.');
            $table->double('mileage_allowance', 8, 2)->nullable()->comment('For a new customer load the data from the config.');
            $table->text('comment')->nullable()->comment('Kommentar');
            $table->bigInteger('edit_user_id')->comment('The ID of the user who last edited the record');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}

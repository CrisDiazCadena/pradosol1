<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // name
            $table->string('lastname'); //Last name
            $table->string('type_beneficiary'); //Last name
            $table->string('identification_card'); //DNI
            $table->string('verified'); //Beneficiary verified
            $table->Integer('partner_id')->unsigned(); //foreing key
            $table->integer('type_identification_id')->unsigned();

            $table->foreign('type_identification_id')->references('id')->on('type_identification_users')->onDelete('cascade');
            $table->foreign('partner_id')->references('id')->on('partners')->onDelete("cascade"); //Reference to partners table

            $table->timestamps(); //create timer, modified timer
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('beneficiaries');
    }
};

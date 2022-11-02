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
            //$table->engine="InnoDB"; //Specify the table storage engine
            $table->id();
            $table->string('name'); // name
            $table->string('lastname'); //Last name
            $table->string('identification_type'); //DNI
            $table->string('identification_card'); //DNI
            $table->timestamps(); //create timer, modified timer
            $table->Integer('socio_id')->unsigned(); //foreing key
            $table->foreign('socio_id')->references('id')->on('partners')->onDelete("cascade"); //Reference to partners table
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

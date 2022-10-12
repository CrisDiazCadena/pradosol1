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
        //
        Schema::create('beneficiarios', function (Blueprint $table) {
            
            $table->engine="InnoDB"; //Specify the table storage engine
            $table->Increments('id');
            $table->Integer('user_id')->unsigned(); //foreing key
            $table->string('name'); //Full name
            $table->string('cedula'); //DNI
            $table->timestamps(); //create timer, modified timer
            $table->foreign('user_id')->references('id')->on('users')->onDelete("cascade"); //Reference to users table
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('beneficiarios');
    }
};

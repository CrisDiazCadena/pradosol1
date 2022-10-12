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
        Schema::create('services', function (Blueprint $table) {
            
            $table->bigIncrements('id');
            $table->string('name'); //service name
            $table->string('servicetype'); //service
            $table->date('startdate'); //service´s start date
            $table->date('enddate'); //service´s end date
            $table->timestamps();//create timer, modified timer
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
        Schema::dropIfExists('services');
    }
};

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
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name'); //event name
            $table->string('eventtype'); //event
            $table->date('startdate'); //event´s start date
            $table->enum('state', ['waiting', 'approved', 'unapproved'])->nullable(false)->default('waiting');
            $table->date('enddate'); //event´s end date
            $table->integer('socio_id')->unsigned();
            $table->foreign('socio_id')->references('id')->on('partners')->onDelete("restrict"); //Reference to users table
            $table->integer('admin_id')->unsigned();
            $table->foreign('admin_id')->references('id')->on('administrators')->onDelete("restrict")->default('1'); //Reference to Administrators table
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
        Schema::dropIfExists('events');
    }
};

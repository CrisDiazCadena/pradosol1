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
        Schema::create('employee_licenses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description')->nullable();
            $table->string('support')->nullable();
            $table->string('verified');
            $table->string('comments')->nullable();
            $table->date('start_license');
            $table->date('end_license');
            $table->integer('type_license_id')->unsigned();
            $table->Integer('employee_id')->unsigned(); //foreing key
            $table->timestamps();

            $table->foreign('type_license_id')->references('id')->on('licenses_types')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete("cascade"); //Reference to partners table

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_licenses');
    }
};

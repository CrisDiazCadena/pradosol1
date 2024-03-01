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
        Schema::create('entrance_tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name'); // name
            $table->Integer('partner_id')->unsigned(); //foreing key
            $table->integer('type_identification_id')->unsigned();
            $table->string('identification_card');

            $table->foreign('type_identification_id')->references('id')->on('type_identification_users')->onDelete('cascade');
            $table->foreign('partner_id')->references('id')->on('partners')->onDelete("cascade"); //Reference to partners table

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
        Schema::dropIfExists('entrance_tickets');
    }
};

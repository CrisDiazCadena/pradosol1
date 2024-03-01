<?php

use App\Models\Partner;
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
        Schema::create('partners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('bonding')->default(Partner::NO_BONDING); //kind of bonding with UIS
            $table->integer('pass')->default(0); //Number of active annual passes
            $table->unsignedTinyInteger('children')->default(0);
            $table->string('marital_status')->default(Partner::SINGLE_STATUS);
            $table->integer('user_id')->unsigned()->nullable(); // Puede ser nula
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');



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
        Schema::dropIfExists('partners');
    }
};

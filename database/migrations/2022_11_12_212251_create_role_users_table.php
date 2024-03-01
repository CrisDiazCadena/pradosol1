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
        Schema::create('role_users', function (Blueprint $table) {

            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');

            /*
            $table->id();
            $table->enum('rol', ['admin', 'partner', 'employee'])->default('partner'); //Role type
            $table->string('position')->nullable(); //Pensioner Active or inactive
            $table->enum('vinculation', ['pensioner', 'worker', 'none', 'active'])->nullable(); //kind of vinculation with UIS
            $table->integer('pass')->nullable(); //Number of active annual passes
            $table->enum('marital_status', ['soltero', 'casado', 'viudo'])->nullable();
            $table->unsignedTinyInteger('childrens')->default(0);
            $table->Integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete("cascade"); //Reference to users table
            $table->unique(['rol','user_id']);
            $table->timestamps();

            */
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_users');
    }
};
